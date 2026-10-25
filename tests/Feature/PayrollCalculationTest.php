<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Payrolls\Pages\EditPayroll;
use App\Filament\App\Resources\Payrolls\Pages\ListPayrolls;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Plan;
use App\Models\SalaryAdvance;
use App\Models\User;
use App\Services\PayrollCalculationService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PayrollCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;

    protected Company $companyB;

    protected User $userA;

    protected User $userB;

    protected PayrollCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PayrollCalculationService::class);

        $plan = Plan::create([
            'name' => 'Gold',
            'slug' => 'gold',
            'price_monthly' => 10000.00,
            'price_yearly' => 100000.00,
            'is_active' => true,
        ]);

        $this->companyA = Company::create([
            'name' => 'Lanka Solar Solutions',
            'slug' => 'lanka-solar-solutions',
            'plan_id' => $plan->id,
            'is_active' => true,
        ]);

        $this->companyB = Company::create([
            'name' => 'Vertex Homes Engineering',
            'slug' => 'vertex-homes',
            'plan_id' => $plan->id,
            'is_active' => true,
        ]);

        $this->userA = User::create([
            'name' => 'Lanka Owner',
            'email' => 'owner@lankasolar.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->userA->companies()->attach($this->companyA->id, ['role' => 'owner']);

        $this->userB = User::create([
            'name' => 'Vertex Admin',
            'email' => 'admin@vertexhomes.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->userB->companies()->attach($this->companyB->id, ['role' => 'owner']);

        $this->actingAs($this->userA);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->companyA);
    }

    public function test_payroll_generation_creates_correct_epf_etf_and_totals(): void
    {
        // Clean out any existing employees in companyA for exact calculation assertion
        Employee::where('company_id', $this->companyA->id)->delete();

        // Employee 1: Basic 100,000, Fixed Allowance 20,000
        $emp1 = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Kamal Jayawardena',
            'designation' => 'Site Supervisor',
            'basic_salary' => 100000.00,
            'fixed_allowance' => 20000.00,
            'joined_date' => '2024-01-01',
            'is_active' => true,
        ]);

        // Employee 2: Basic 85,000, Fixed Allowance 15,000
        $emp2 = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Nimal Siriwardena',
            'designation' => 'Solar Technician',
            'basic_salary' => 85000.00,
            'fixed_allowance' => 15000.00,
            'joined_date' => '2024-02-01',
            'is_active' => true,
        ]);

        $payroll = $this->service->generateMonthlyPayroll($this->companyA->id, '2026-10', $this->userA->id);

        $this->assertNotNull($payroll);
        $this->assertSame('2026-10', $payroll->month);
        $this->assertSame('Payroll - October 2026', $payroll->title);
        $this->assertSame('draft', $payroll->status);
        $this->assertCount(2, $payroll->items);

        // Verify Emp 1: 8% EPF = 8,000, 12% EPF = 12,000, 3% ETF = 3,000
        $item1 = $payroll->items->firstWhere('employee_id', $emp1->id);
        $this->assertNotNull($item1);
        $this->assertEquals(100000.00, (float) $item1->basic_salary);
        $this->assertEquals(20000.00, (float) $item1->fixed_allowance);
        $this->assertEquals(120000.00, (float) $item1->gross_salary);
        $this->assertEquals(8000.00, (float) $item1->epf_employee);
        $this->assertEquals(12000.00, (float) $item1->epf_employer);
        $this->assertEquals(3000.00, (float) $item1->etf_employer);
        $this->assertEquals(0.00, (float) $item1->advance_deduction);
        $this->assertEquals(8000.00, (float) $item1->total_deductions);
        $this->assertEquals(112000.00, (float) $item1->net_salary);

        // Verify Emp 2: 8% EPF = 6,800, 12% EPF = 10,200, 3% ETF = 2,550
        $item2 = $payroll->items->firstWhere('employee_id', $emp2->id);
        $this->assertNotNull($item2);
        $this->assertEquals(85000.00, (float) $item2->basic_salary);
        $this->assertEquals(15000.00, (float) $item2->fixed_allowance);
        $this->assertEquals(100000.00, (float) $item2->gross_salary);
        $this->assertEquals(6800.00, (float) $item2->epf_employee);
        $this->assertEquals(10200.00, (float) $item2->epf_employer);
        $this->assertEquals(2550.00, (float) $item2->etf_employer);
        $this->assertEquals(6800.00, (float) $item2->total_deductions);
        $this->assertEquals(93200.00, (float) $item2->net_salary);

        // Verify Parent Payroll Totals
        $this->assertEquals(185000.00, (float) $payroll->total_basic);
        $this->assertEquals(35000.00, (float) $payroll->total_allowances);
        $this->assertEquals(220000.00, (float) $payroll->total_gross);
        $this->assertEquals(14800.00, (float) $payroll->total_epf_employee);
        $this->assertEquals(22200.00, (float) $payroll->total_epf_employer);
        $this->assertEquals(5550.00, (float) $payroll->total_etf_employer);
        $this->assertEquals(0.00, (float) $payroll->total_advances);
        $this->assertEquals(14800.00, (float) $payroll->total_deductions);
        $this->assertEquals(205200.00, (float) $payroll->total_net_payable);
        $this->assertEquals(42550.00, $payroll->total_epf_etf);
    }

    public function test_approved_salary_advances_are_automatically_pulled_and_deducted(): void
    {
        Employee::where('company_id', $this->companyA->id)->delete();

        $employee = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Saman Perera',
            'designation' => 'Solar Technician',
            'basic_salary' => 80000.00,
            'fixed_allowance' => 10000.00,
            'joined_date' => '2024-03-01',
            'is_active' => true,
        ]);

        // Advance 1: Approved for target month 2026-10 -> SHOULD DEDUCT
        $adv1 = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => 15000.00,
            'request_date' => '2026-10-05',
            'deduct_month' => '2026-10',
            'reason' => 'Emergency bill',
            'status' => 'approved',
            'approved_by' => $this->userA->id,
        ]);

        // Advance 2: Pending for target month 2026-10 -> MUST NOT DEDUCT
        $adv2 = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => 5000.00,
            'request_date' => '2026-10-06',
            'deduct_month' => '2026-10',
            'reason' => 'Pending request',
            'status' => 'pending',
        ]);

        // Advance 3: Approved for different month 2026-11 -> MUST NOT DEDUCT
        $adv3 = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => 8000.00,
            'request_date' => '2026-10-07',
            'deduct_month' => '2026-11',
            'reason' => 'Next month advance',
            'status' => 'approved',
            'approved_by' => $this->userA->id,
        ]);

        $payroll = $this->service->generateMonthlyPayroll($this->companyA->id, '2026-10', $this->userA->id);
        $item = $payroll->items->firstWhere('employee_id', $employee->id);

        $this->assertNotNull($item);
        // EPF 8% on 80,000 = 6,400.00
        $this->assertEquals(6400.00, (float) $item->epf_employee);
        // Advance deduction must be exactly 15,000.00
        $this->assertEquals(15000.00, (float) $item->advance_deduction);
        // Total Deductions = 6,400 + 15,000 = 21,400.00
        $this->assertEquals(21400.00, (float) $item->total_deductions);
        // Net Salary = 90,000 - 21,400 = 68,600.00
        $this->assertEquals(68600.00, (float) $item->net_salary);

        // Confirm Payroll and verify advances update
        $this->service->confirmPayroll($payroll, $this->userA->id);

        $payroll->refresh();
        $this->assertSame('confirmed', $payroll->status);

        $adv1->refresh();
        $adv2->refresh();
        $adv3->refresh();

        $this->assertSame('deducted', $adv1->status, 'Approved advance for 2026-10 should be transitioned to deducted');
        $this->assertSame('pending', $adv2->status, 'Pending advance must remain pending');
        $this->assertSame('approved', $adv3->status, 'Advance for 2026-11 must remain approved');
    }

    public function test_inactive_employees_are_excluded_from_payroll(): void
    {
        Employee::where('company_id', $this->companyA->id)->delete();

        $activeEmp = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Active Employee',
            'designation' => 'Office Staff',
            'basic_salary' => 70000.00,
            'is_active' => true,
        ]);

        $inactiveEmp = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Resigned Employee',
            'designation' => 'Office Staff',
            'basic_salary' => 60000.00,
            'is_active' => false,
        ]);

        $payroll = $this->service->generateMonthlyPayroll($this->companyA->id, '2026-10', $this->userA->id);

        $this->assertTrue($payroll->items->contains('employee_id', $activeEmp->id));
        $this->assertFalse($payroll->items->contains('employee_id', $inactiveEmp->id));
    }

    public function test_payroll_tenant_boundary_isolation(): void
    {
        Employee::where('company_id', $this->companyA->id)->delete();
        Employee::where('company_id', $this->companyB->id)->delete();

        $empA = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Company A Tech',
            'designation' => 'Solar Technician',
            'basic_salary' => 80000.00,
            'is_active' => true,
        ]);

        $empB = Employee::create([
            'company_id' => $this->companyB->id,
            'name' => 'Company B Tech',
            'designation' => 'Electrician',
            'basic_salary' => 90000.00,
            'is_active' => true,
        ]);

        $payrollA = $this->service->generateMonthlyPayroll($this->companyA->id, '2026-10', $this->userA->id);

        // Switch to Company B
        Filament::setTenant($this->companyB);
        $this->actingAs($this->userB);

        $payrollB = $this->service->generateMonthlyPayroll($this->companyB->id, '2026-10', $this->userB->id);

        $bPayrolls = Payroll::all();
        $this->assertTrue($bPayrolls->contains('id', $payrollB->id));
        $this->assertFalse($bPayrolls->contains('id', $payrollA->id), 'Company B query must not see Company A payroll');

        // Company B Livewire test
        Livewire::test(ListPayrolls::class)
            ->assertCanSeeTableRecords([$payrollB])
            ->assertCanNotSeeTableRecords([$payrollA]);
    }

    public function test_editing_payroll_item_recalculates_gross_deductions_and_parent_totals(): void
    {
        Employee::where('company_id', $this->companyA->id)->delete();

        $emp = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Ruwan Bandara',
            'designation' => 'Solar Technician',
            'basic_salary' => 80000.00,
            'fixed_allowance' => 10000.00,
            'is_active' => true,
        ]);

        $payroll = $this->service->generateMonthlyPayroll($this->companyA->id, '2026-10', $this->userA->id);
        $item = $payroll->items->first();

        // Initial net = 90,000 - 6,400 = 83,600
        $this->assertEquals(83600.00, (float) $item->net_salary);
        $this->assertEquals(83600.00, (float) $payroll->total_net_payable);

        // Add additional site OT allowance of 10,000 and other deductions of 2,000
        $item->update([
            'additional_allowance' => 10000.00,
            'other_deductions' => 2000.00,
        ]);

        $item->refresh();
        // Gross: 80,000 + 10,000 + 10,000 = 100,000
        $this->assertEquals(100000.00, (float) $item->gross_salary);
        // Deductions: 6,400 (EPF) + 2,000 (other) = 8,400
        $this->assertEquals(8400.00, (float) $item->total_deductions);
        // Net: 100,000 - 8,400 = 91,600
        $this->assertEquals(91600.00, (float) $item->net_salary);

        $payroll->refresh();
        $this->assertEquals(100000.00, (float) $payroll->total_gross);
        $this->assertEquals(8400.00, (float) $payroll->total_deductions);
        $this->assertEquals(91600.00, (float) $payroll->total_net_payable);
    }

    public function test_filament_livewire_generate_monthly_payroll_modal_action(): void
    {
        Employee::where('company_id', $this->companyA->id)->delete();

        Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Lalith Gamage',
            'designation' => 'Electrician',
            'basic_salary' => 75000.00,
            'is_active' => true,
        ]);

        Livewire::test(ListPayrolls::class)
            ->callAction('generate_payroll', [
                'month' => '2026-11',
            ])
            ->assertHasNoActionErrors();

        $payroll = Payroll::where('company_id', $this->companyA->id)
            ->where('month', '2026-11')
            ->firstOrFail();

        $this->assertSame('draft', $payroll->status);
        $this->assertSame('Payroll - November 2026', $payroll->title);
        $this->assertCount(1, $payroll->items);

        // Test Edit Page finalize action
        Livewire::test(EditPayroll::class, ['record' => $payroll->id])
            ->callAction('confirm_payroll')
            ->assertHasNoActionErrors();

        $payroll->refresh();
        $this->assertSame('confirmed', $payroll->status);
    }
}
