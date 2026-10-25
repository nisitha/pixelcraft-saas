<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Payrolls\Pages\EditPayroll;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Plan;
use App\Models\SalaryAdvance;
use App\Models\User;
use App\Services\GeminiAssistantService;
use App\Services\PayrollCalculationService;
use DomainException;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PayrollFinalizationAndAiTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;

    protected Company $companyB;

    protected User $userA;

    protected User $userB;

    protected PayrollCalculationService $payrollService;

    protected GeminiAssistantService $geminiService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payrollService = app(PayrollCalculationService::class);
        $this->geminiService = app(GeminiAssistantService::class);

        $plan = Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'price_monthly' => 15000.00,
            'price_yearly' => 150000.00,
            'is_active' => true,
        ]);

        $this->companyA = Company::create([
            'name' => 'Vertex Home Solutions (Pvt) Ltd',
            'slug' => 'vertex-home-solutions',
            'plan_id' => $plan->id,
            'address' => 'Mirigama, Western Province, Sri Lanka',
            'phone' => '+94 77 123 4567',
            'email' => 'accounts@vertexhomes.lk',
            'tax_number' => 'PV-0012984',
            'is_active' => true,
        ]);

        $this->companyB = Company::create([
            'name' => 'Competitor Solar Tech',
            'slug' => 'competitor-solar-tech',
            'plan_id' => $plan->id,
            'address' => 'Colombo, Sri Lanka',
            'is_active' => true,
        ]);

        $this->userA = User::create([
            'name' => 'Vertex Director',
            'email' => 'director@vertexhomes.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->userA->companies()->attach($this->companyA->id, ['role' => 'owner']);

        $this->userB = User::create([
            'name' => 'Competitor Admin',
            'email' => 'admin@competitor.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->userB->companies()->attach($this->companyB->id, ['role' => 'owner']);

        $this->actingAs($this->userA);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->companyA);
    }

    public function test_marking_payroll_as_paid_creates_automated_expense_and_prevents_duplicate_entry(): void
    {
        // Setup 2 employees in Company A
        $emp1 = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Kasun Perera',
            'nic_number' => '199212345678',
            'designation' => 'Solar Technician',
            'basic_salary' => 100000.00,
            'fixed_allowance' => 20000.00,
            'epf_number' => 'EPF-7891',
            'bank_name' => 'Commercial Bank',
            'bank_account_number' => '8001234567',
            'joined_date' => '2024-01-15',
            'is_active' => true,
        ]);

        $emp2 = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Nimal Jayawardena',
            'nic_number' => '198898765432',
            'designation' => 'Office Staff',
            'basic_salary' => 60000.00,
            'fixed_allowance' => 10000.00,
            'epf_number' => 'EPF-7892',
            'bank_name' => 'Sampath Bank',
            'bank_account_number' => '1029384756',
            'joined_date' => '2023-05-10',
            'is_active' => true,
        ]);

        // Generate payroll
        $payroll = $this->payrollService->generateMonthlyPayroll($this->companyA->id, '2026-10', $this->userA->id);

        // Expected calculations:
        // emp1: basic 100k, allowance 20k -> gross 120k. epf_emp(8%)=8k, epf_empr(12%)=12k, etf_empr(3%)=3k
        // emp2: basic 60k, allowance 10k -> gross 70k. epf_emp(8%)=4.8k, epf_empr(12%)=7.2k, etf_empr(3%)=1.8k
        // total_gross = 190,000
        // total_epf_employer = 19,200
        // total_etf_employer = 4,800
        // Total Employer Cost (CTC) = 190,000 + 19,200 + 4,800 = 214,000.00
        $this->assertEquals(190000.00, (float) $payroll->total_gross);
        $this->assertEquals(19200.00, (float) $payroll->total_epf_employer);
        $this->assertEquals(4800.00, (float) $payroll->total_etf_employer);

        $expense = $this->payrollService->markAsPaidAndRecordExpense($payroll, [
            'payment_date' => '2026-10-31',
            'payment_method' => 'bank_transfer',
            'category' => 'Salaries & Wages',
            'notes' => 'Monthly staff remuneration direct bank transfer',
        ], $this->userA->id);

        // Assert Expense created in expenses table
        $this->assertInstanceOf(Expense::class, $expense);
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'company_id' => $this->companyA->id,
            'category' => 'Salaries & Wages',
            'title' => 'Monthly Staff Payroll - 2026-10',
            'amount' => 214000.00,
            'reference_no' => 'PAY-2026-10',
            'is_internal_overhead' => false,
        ]);

        // Assert Payroll status updated to 'paid'
        $this->assertEquals('paid', $payroll->fresh()->status);

        // Double-entry prevention: trying to call markAsPaidAndRecordExpense again must throw DomainException
        $this->expectException(DomainException::class);
        $this->payrollService->markAsPaidAndRecordExpense($payroll->fresh(), [
            'payment_date' => '2026-10-31',
        ], $this->userA->id);
    }

    public function test_filament_mark_as_paid_action_creates_expense_from_ui(): void
    {
        $emp = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Sunil Shantha',
            'designation' => 'Site Supervisor',
            'basic_salary' => 80000.00,
            'fixed_allowance' => 15000.00,
            'joined_date' => '2024-02-01',
            'is_active' => true,
        ]);

        $payroll = $this->payrollService->generateMonthlyPayroll($this->companyA->id, '2026-11', $this->userA->id);

        Livewire::test(EditPayroll::class, ['record' => $payroll->id])
            ->callAction('mark_as_paid', [
                'payment_date' => '2026-11-30',
                'payment_method' => 'bank_transfer',
                'category' => 'Salaries & Wages',
                'notes' => 'Processed via UI',
            ])
            ->assertHasNoActionErrors();

        $this->assertEquals('paid', $payroll->fresh()->status);
        $this->assertDatabaseHas('expenses', [
            'company_id' => $this->companyA->id,
            'reference_no' => 'PAY-2026-11',
            'category' => 'Salaries & Wages',
        ]);
    }

    public function test_payslip_pdf_endpoints_return_200_and_valid_pdf_stream(): void
    {
        $emp = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Kamal Gunaratne',
            'nic_number' => '199012345678',
            'designation' => 'Senior Solar Technician',
            'basic_salary' => 120000.00,
            'fixed_allowance' => 25000.00,
            'epf_number' => 'EPF-9090',
            'bank_name' => 'Commercial Bank',
            'bank_branch' => 'Mirigama',
            'bank_account_number' => '8009876543',
            'joined_date' => '2023-01-10',
            'is_active' => true,
        ]);

        $payroll = $this->payrollService->generateMonthlyPayroll($this->companyA->id, '2026-09', $this->userA->id);
        $item = $payroll->items()->where('employee_id', $emp->id)->firstOrFail();

        // Single payslip download
        $response = $this->get(route('payroll.payslip.download', $item));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());

        // Single payslip stream
        $streamResponse = $this->get(route('payroll.payslip.download', ['payrollItem' => $item, 'stream' => 1]));
        $streamResponse->assertStatus(200);
        $streamResponse->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $streamResponse->getContent());

        // Combined payslips download
        $combinedResponse = $this->get(route('payroll.payslips.all', $payroll));
        $combinedResponse->assertStatus(200);
        $combinedResponse->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $combinedResponse->getContent());

        // Tenant isolation: User B from Company B cannot access Company A's payslip
        $this->actingAs($this->userB);
        Filament::setTenant($this->companyB);

        $unauthorizedResponse = $this->get(route('payroll.payslip.download', $item));
        $this->assertTrue(in_array($unauthorizedResponse->status(), [403, 404]));
    }

    public function test_gemini_assistant_get_payroll_summary_tool_returns_accurate_totals(): void
    {
        // Clean out any existing employees in Company A
        Employee::where('company_id', $this->companyA->id)->delete();

        // Create 2 Solar Technicians and 1 Office Staff
        $tech1 = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Nuwan Pradeep',
            'designation' => 'Solar Technician',
            'basic_salary' => 90000.00,
            'fixed_allowance' => 15000.00,
            'joined_date' => '2024-03-01',
            'is_active' => true,
        ]);

        $tech2 = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Saman Kumara',
            'designation' => 'Solar Technician',
            'basic_salary' => 90000.00,
            'fixed_allowance' => 15000.00,
            'joined_date' => '2024-03-01',
            'is_active' => true,
        ]);

        $office = Employee::create([
            'company_id' => $this->companyA->id,
            'name' => 'Dilani Wijesinghe',
            'designation' => 'Office Staff',
            'basic_salary' => 70000.00,
            'fixed_allowance' => 10000.00,
            'joined_date' => '2023-08-01',
            'is_active' => true,
        ]);

        // Add advance for Nuwan: 10,000
        SalaryAdvance::create([
            'company_id' => $this->companyA->id,
            'employee_id' => $tech1->id,
            'amount' => 10000.00,
            'request_date' => '2026-10-05',
            'deduct_month' => '2026-10',
            'reason' => 'Emergency medical expense',
            'status' => 'approved',
            'approved_by' => $this->userA->id,
        ]);

        $payroll = $this->payrollService->generateMonthlyPayroll($this->companyA->id, '2026-10', $this->userA->id);
        $this->payrollService->confirmPayroll($payroll, $this->userA->id);

        // Expected totals:
        // Basic: 90k + 90k + 70k = 250,000.00
        // Allowances: 15k + 15k + 10k = 40,000.00
        // Gross: 290,000.00
        // EPF Employee (8% on 250k) = 20,000.00
        // EPF Employer (12% on 250k) = 30,000.00
        // ETF Employer (3% on 250k) = 7,500.00
        // Advances: 10,000.00
        // Net: Gross(290k) - Deductions(EPF 20k + Advance 10k) = 260,000.00
        // Employer Cost (CTC): 290,000 + 30,000 + 7,500 = 327,500.00

        // Execute tool via GeminiAssistantService
        $result = $this->geminiService->executeTool('get_payroll_summary', [
            'month' => '2026-10',
        ], $this->companyA->id);

        $this->assertTrue($result['found']);
        $this->assertEquals('2026-10', $result['month']);
        $this->assertEquals('confirmed', $result['status']);
        $this->assertEquals(3, $result['total_employees']);
        $this->assertEquals(250000.00, $result['total_basic_salary_lkr']);
        $this->assertEquals(40000.00, $result['total_allowances_lkr']);
        $this->assertEquals(290000.00, $result['total_gross_salary_lkr']);
        $this->assertEquals(260000.00, $result['total_net_salary_disbursed_lkr']);
        $this->assertEquals(10000.00, $result['total_salary_advances_recovered_lkr']);
        $this->assertEquals(20000.00, $result['statutory_liabilities_lkr']['epf_employee_8_pct']);
        $this->assertEquals(30000.00, $result['statutory_liabilities_lkr']['epf_employer_12_pct']);
        $this->assertEquals(7500.00, $result['statutory_liabilities_lkr']['etf_employer_3_pct']);
        $this->assertEquals(37500.00, $result['statutory_liabilities_lkr']['employer_statutory_contribution']);
        $this->assertEquals(327500.00, $result['total_employer_cost_lkr']);

        // Check designation breakdown
        $this->assertCount(2, $result['breakdown_by_designation']);
        $techSummary = collect($result['breakdown_by_designation'])->firstWhere('designation', 'Solar Technician');
        $this->assertNotNull($techSummary);
        $this->assertEquals(2, $techSummary['employee_count']);
        $this->assertEquals(180000.00, $techSummary['total_basic_lkr']);
        $this->assertEquals(210000.00, $techSummary['total_gross_lkr']);
        $this->assertEquals(10000.00, $techSummary['advances_recovered_lkr']);

        // Test without month argument: defaults to latest finalized
        $defaultResult = $this->geminiService->executeTool('get_payroll_summary', [], $this->companyA->id);
        $this->assertTrue($defaultResult['found']);
        $this->assertEquals('2026-10', $defaultResult['month']);

        // Test tenant isolation: Company B has no payroll records
        $companyBResult = $this->geminiService->executeTool('get_payroll_summary', [
            'month' => '2026-10',
        ], $this->companyB->id);
        $this->assertFalse($companyBResult['found']);
    }
}
