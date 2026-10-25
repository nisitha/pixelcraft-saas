<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Employees\Pages\CreateEmployee;
use App\Filament\App\Resources\Employees\Pages\EditEmployee;
use App\Filament\App\Resources\Employees\Pages\ListEmployees;
use App\Filament\App\Resources\SalaryAdvances\Pages\ListSalaryAdvances;
use App\Models\Company;
use App\Models\Employee;
use App\Models\PayrollItem;
use App\Models\Plan;
use App\Models\SalaryAdvance;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PayrollEmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;

    protected Company $companyB;

    protected User $userA;

    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->companyA = Company::where('slug', 'lanka-solar-solutions')->first()
            ?? Company::create([
                'name' => 'Lanka Solar Solutions',
                'slug' => 'lanka-solar-solutions',
                'plan_id' => Plan::first()?->id ?? 1,
                'is_active' => true,
            ]);

        $this->companyB = Company::where('slug', 'vertex-homes')->first()
            ?? Company::create([
                'name' => 'Vertex Homes Engineering',
                'slug' => 'vertex-homes',
                'plan_id' => Plan::first()?->id ?? 1,
                'is_active' => true,
            ]);

        $this->userA = User::where('email', 'owner@lankasolar.lk')->first()
            ?? User::create([
                'name' => 'Lanka Owner',
                'email' => 'owner@lankasolar.lk',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);

        $this->userB = User::create([
            'name' => 'Vertex Homes Admin',
            'email' => 'admin@vertexhomes.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->userB->companies()->syncWithoutDetaching([$this->companyB->id => ['role' => 'owner']]);

        $this->actingAs($this->userA);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->companyA);
    }

    public function test_can_create_employee_with_payroll_fields_and_relationships(): void
    {
        $employee = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Saman Kumara Wickramasinghe',
            'calling_name' => 'Saman',
            'nic_number' => '199220491823',
            'designation' => 'Solar Technician',
            'contact_number' => '0771234567',
            'daily_rate' => 3500.00,
            'basic_salary' => 85000.00,
            'fixed_allowance' => 15000.00,
            'epf_number' => 'EPF-8812',
            'bank_name' => 'Commercial Bank of Ceylon',
            'bank_branch' => 'Colombo Fort',
            'bank_account_number' => '8001928374',
            'joined_date' => '2024-03-15',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'company_id' => $this->companyA->id,
            'name' => 'Saman Kumara Wickramasinghe',
            'calling_name' => 'Saman',
            'designation' => 'Solar Technician',
            'epf_number' => 'EPF-8812',
        ]);

        $this->assertSame('85000.00', (string) $employee->basic_salary);
        $this->assertSame('15000.00', (string) $employee->fixed_allowance);
        $this->assertSame('3500.00', (string) $employee->daily_rate);
        $this->assertTrue($employee->is_active);
        $this->assertEquals($this->companyA->id, $employee->company->id);
        $this->assertCount(0, $employee->salaryAdvances);
        $this->assertInstanceOf(HasMany::class, $employee->payrollItems());
        $this->assertInstanceOf(PayrollItem::class, $employee->payrollItems()->getRelated());
    }

    public function test_employee_tenant_isolation_on_creation_and_query(): void
    {
        // 1. Automatic tenant assignment when Filament::getTenant() is active
        $empA = Employee::create([
            'name' => 'Nuwan Pradeep',
            'calling_name' => 'Nuwan',
            'designation' => 'Site Supervisor',
            'basic_salary' => 110000.00,
            'joined_date' => now()->toDateString(),
        ]);

        $this->assertEquals($this->companyA->id, $empA->company_id, 'Employee should automatically receive active company ID');

        // 2. Switch tenant to Company B
        Filament::setTenant($this->companyB);
        $this->actingAs($this->userB);

        $empB = Employee::create([
            'name' => 'Kasun Thilina',
            'calling_name' => 'Kasun',
            'designation' => 'Electrician',
            'basic_salary' => 75000.00,
            'joined_date' => now()->toDateString(),
        ]);

        $this->assertEquals($this->companyB->id, $empB->company_id);

        // 3. Querying under Company B tenant must NOT see Company A employee
        $companyBEmployees = Employee::all();
        $this->assertTrue($companyBEmployees->contains('id', $empB->id));
        $this->assertFalse($companyBEmployees->contains('id', $empA->id), 'Company B query must not return Company A employee');

        // 4. Switch back to Company A
        Filament::setTenant($this->companyA);
        $this->actingAs($this->userA);

        $companyAEmployees = Employee::all();
        $this->assertTrue($companyAEmployees->contains('id', $empA->id));
        $this->assertFalse($companyAEmployees->contains('id', $empB->id), 'Company A query must not return Company B employee');
    }

    public function test_employee_crud_via_filament_livewire(): void
    {
        // Test Create Page
        Livewire::test(CreateEmployee::class)
            ->fillForm([
                'name' => 'Sunil Shantha Silva',
                'calling_name' => 'Sunil',
                'nic_number' => '198810294819',
                'designation' => 'Office Staff',
                'contact_number' => '0719876543',
                'basic_salary' => 65000.00,
                'fixed_allowance' => 10000.00,
                'epf_number' => 'EPF-9941',
                'bank_name' => 'Sampath Bank',
                'bank_branch' => 'Negombo',
                'bank_account_number' => '1004928172',
                'joined_date' => '2025-01-10',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $employee = Employee::where('nic_number', '198810294819')->firstOrFail();
        $this->assertSame('Sunil Shantha Silva', $employee->name);
        $this->assertSame('Office Staff', $employee->designation);
        $this->assertEquals($this->companyA->id, $employee->company_id);

        // Test List Page records and actions
        Livewire::test(ListEmployees::class)
            ->assertCanSeeTableRecords([$employee])
            ->callTableAction('toggle_active', $employee);

        $employee->refresh();
        $this->assertFalse($employee->is_active, 'Employee status should toggle to inactive');

        // Test Edit Page
        Livewire::test(EditEmployee::class, ['record' => $employee->id])
            ->fillForm([
                'designation' => 'Site Supervisor',
                'basic_salary' => 95000.00,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $employee->refresh();
        $this->assertSame('Site Supervisor', $employee->designation);
        $this->assertEquals(95000.00, (float) $employee->basic_salary);
    }

    public function test_salary_advance_creation_and_relationships(): void
    {
        $employee = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Chathura Bandara',
            'calling_name' => 'Chathura',
            'designation' => 'Solar Technician',
            'basic_salary' => 80000.00,
            'joined_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $advance = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => 15000.00,
            'request_date' => '2026-10-15',
            'deduct_month' => '2026-10',
            'reason' => 'Family medical emergency',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('salary_advances', [
            'id' => $advance->id,
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => '15000.00',
            'deduct_month' => '2026-10',
            'status' => 'pending',
        ]);

        $this->assertEquals($employee->id, $advance->employee->id);
        $this->assertEquals($this->companyA->id, $advance->company->id);
        $this->assertTrue($employee->salaryAdvances->contains('id', $advance->id));
        $this->assertNull($advance->approver);
    }

    public function test_salary_advance_single_click_approval_action(): void
    {
        $employee = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Janaka Priyantha',
            'designation' => 'Electrician',
            'basic_salary' => 70000.00,
            'joined_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $advance = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => 20000.00,
            'request_date' => '2026-10-18',
            'deduct_month' => '2026-10',
            'reason' => 'Motorbike repair for site visits',
            'status' => 'pending',
        ]);

        // Call single click Approve action
        Livewire::test(ListSalaryAdvances::class)
            ->callTableAction('approve', $advance);

        $advance->refresh();
        $this->assertSame('approved', $advance->status);
        $this->assertEquals($this->userA->id, $advance->approved_by);
        $this->assertEquals($this->userA->name, $advance->approver->name);
    }

    public function test_salary_advance_single_click_rejection_action(): void
    {
        $employee = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Mahesh Senanayake',
            'designation' => 'Solar Technician',
            'basic_salary' => 60000.00,
            'joined_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $advance = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => 25000.00,
            'request_date' => '2026-10-20',
            'deduct_month' => '2026-10',
            'reason' => 'Advance without prior notice',
            'status' => 'pending',
        ]);

        // Call single click Reject action
        Livewire::test(ListSalaryAdvances::class)
            ->callTableAction('reject', $advance);

        $advance->refresh();
        $this->assertSame('rejected', $advance->status);
    }

    public function test_salary_advance_tenant_isolation(): void
    {
        $empA = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Company A Tech',
            'designation' => 'Solar Technician',
            'basic_salary' => 80000.00,
            'joined_date' => now()->toDateString(),
        ]);

        $advanceA = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $empA->id,
            'amount' => 10000.00,
            'request_date' => now()->toDateString(),
            'deduct_month' => '2026-10',
            'status' => 'pending',
        ]);

        // Switch to Company B
        Filament::setTenant($this->companyB);
        $this->actingAs($this->userB);

        $empB = Employee::create([
            'company_id' => $this->companyB->id,
            'name' => 'Company B Tech',
            'designation' => 'Electrician',
            'basic_salary' => 85000.00,
            'joined_date' => now()->toDateString(),
        ]);

        $advanceB = SalaryAdvance::create([
            'company_id' => $this->companyB->id,
            'employee_id' => $empB->id,
            'amount' => 12000.00,
            'request_date' => now()->toDateString(),
            'deduct_month' => '2026-10',
            'status' => 'pending',
        ]);

        // Querying under Company B
        $bAdvances = SalaryAdvance::all();
        $this->assertTrue($bAdvances->contains('id', $advanceB->id));
        $this->assertFalse($bAdvances->contains('id', $advanceA->id), 'Company B must not see Company A salary advance');

        // Switch back to Company A
        Filament::setTenant($this->companyA);
        $this->actingAs($this->userA);

        $aAdvances = SalaryAdvance::all();
        $this->assertTrue($aAdvances->contains('id', $advanceA->id));
        $this->assertFalse($aAdvances->contains('id', $advanceB->id), 'Company A must not see Company B salary advance');
    }

    public function test_salary_advance_cascade_on_employee_delete(): void
    {
        $employee = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Temporary Worker',
            'designation' => 'Solar Technician',
            'basic_salary' => 50000.00,
            'joined_date' => now()->toDateString(),
        ]);

        $advance = SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $employee->id,
            'amount' => 8000.00,
            'request_date' => now()->toDateString(),
            'deduct_month' => '2026-10',
            'status' => 'pending',
        ]);

        $advanceId = $advance->id;
        $employee->delete();

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
        $this->assertDatabaseMissing('salary_advances', ['id' => $advanceId]);
    }
}
