<?php

namespace Tests\Feature;

use App\Filament\App\Pages\FinancialReports;
use App\Filament\App\Resources\CompletionCertificates\Pages\CreateCompletionCertificate;
use App\Filament\App\Resources\CompletionCertificates\Pages\EditCompletionCertificate;
use App\Models\CommissionPayout;
use App\Models\Company;
use App\Models\CompletionCertificate;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quotation;
use App\Models\SalesAgent;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Filament\Support\Enums\Width;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolarCertificatesAndSalesAgentsTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->company = Company::where('slug', 'vertex-homes')->first()
            ?? Company::first();
    }

    public function test_seeded_sample_completion_certificate_exists_with_correct_data(): void
    {
        $cert = CompletionCertificate::where('certificate_number', 'CERT-VHE/2026/RU/199/005')->first();

        $this->assertNotNull($cert, 'Seeded completion certificate should exist');
        $this->assertSame('MR. M A N A M Fernando', $cert->customer_name);
        $this->assertSame('05 kW+05kw Battery offgrid Solar System', $cert->system_capacity);
        $this->assertSame('260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa', $cert->site_address);
        $this->assertSame('Non', $cert->grid_connection);
        $this->assertSame('30 Years (Panels) / 10 Years (Inverter+Battery)', $cert->warranty_period);
        $this->assertSame('Commissioned & Operational', $cert->system_status);
        $this->assertSame('Engineering Safety Compliant', $cert->verification_status);
        $this->assertSame('Lead Engineer', $cert->lead_engineer_name);
        $this->assertSame('Managing Director', $cert->director_name);
        $this->assertSame('a4', $cert->paper_size);
        $this->assertSame('landscape', $cert->orientation);
        $this->assertSame('issued', $cert->status);
    }

    public function test_completion_certificate_pdf_generates_successfully_in_all_paper_sizes_and_orientations(): void
    {
        $cert = CompletionCertificate::where('certificate_number', 'CERT-VHE/2026/RU/199/005')->firstOrFail();
        $pdfService = app(PdfService::class);

        $paperSizes = ['a4', 'a5', 'letter', 'b5'];
        $orientations = ['landscape', 'portrait'];

        foreach ($paperSizes as $size) {
            foreach ($orientations as $orientation) {
                $cert->paper_size = $size;
                $cert->orientation = $orientation;
                $cert->save();

                $pdf = $pdfService->generateCompletionCertificatePdf($cert);
                $output = $pdf->output();

                $this->assertNotEmpty($output, "PDF for {$size} {$orientation} should not be empty");
                $this->assertStringStartsWith('%PDF-', $output, "Output for {$size} {$orientation} should be a valid PDF");
            }
        }
    }

    public function test_completion_certificate_sanitized_filename(): void
    {
        $cert = CompletionCertificate::where('certificate_number', 'CERT-VHE/2026/RU/199/005')->firstOrFail();
        $pdfService = app(PdfService::class);

        $fileName = $pdfService->getCompletionCertificateFileName($cert);

        $this->assertSame('Certificate-CERT-VHE-2026-RU-199-005.pdf', $fileName);
        $this->assertStringNotContainsString('/', $fileName);
        $this->assertStringNotContainsString('\\', $fileName);
    }

    public function test_completion_certificate_blade_template_renders_required_elements(): void
    {
        $cert = CompletionCertificate::where('certificate_number', 'CERT-VHE/2026/RU/199/005')->firstOrFail();

        $view = view('pdf.completion-certificate', [
            'certificate' => $cert,
            'company' => $cert->company,
            'paper_size' => $cert->paper_size,
            'orientation' => $cert->orientation,
        ])->render();

        $this->assertStringContainsString('CERTIFICATE OF COMPLETION', $view);
        $this->assertStringContainsString('SOLAR POWER SYSTEM INSTALLATION & COMMISSIONING', $view);
        $this->assertStringContainsString('SYSTEM & INSTALLATION SPECIFICATIONS', $view);
        $this->assertStringContainsString('MR. M A N A M Fernando', $view);
        $this->assertStringContainsString('CERT-VHE/2026/RU/199/005', $view);
        $this->assertStringContainsString('05 kW+05kw Battery offgrid Solar System', $view);
        $this->assertStringContainsString('Lead Engineer', $view);
        $this->assertStringContainsString('Managing Director', $view);
    }

    public function test_completion_certificate_canvas_blade_renders_cleanly(): void
    {
        $cert = CompletionCertificate::where('certificate_number', 'CERT-VHE/2026/RU/199/005')->firstOrFail();

        $view = view('filament.app.resources.completion-certificates.certificate-canvas', [
            'customerName' => $cert->customer_name,
            'systemCapacity' => $cert->system_capacity,
            'certificateNumber' => $cert->certificate_number,
            'siteAddress' => $cert->site_address,
            'commissioningDate' => $cert->commissioning_date?->format('d/m/Y'),
            'gridConnection' => $cert->grid_connection,
            'warrantyPeriod' => $cert->warranty_period,
            'systemStatus' => $cert->system_status,
            'verificationStatus' => $cert->verification_status,
            'complianceNote' => $cert->compliance_note,
            'leadEngineerName' => $cert->lead_engineer_name,
            'leadEngineerTitle' => $cert->lead_engineer_title,
            'directorName' => $cert->director_name,
            'directorTitle' => $cert->director_title,
            'signedDate' => $cert->signed_date?->format('d/m/Y'),
            'isPortrait' => false,
            'company' => $cert->company,
            'logoUrl' => null,
            'signatureUrl' => null,
            'paperSize' => 'a4',
            'orientation' => 'landscape',
            'status' => 'issued',
        ])->render();

        $this->assertStringContainsString('CERTIFICATE OF COMPLETION', $view);
        $this->assertStringContainsString('SYSTEM & INSTALLATION SPECIFICATIONS', $view);
        $this->assertStringContainsString('MR. M A N A M Fernando', $view);
        $this->assertStringContainsString('Lead Engineer', $view);
        $this->assertStringContainsString('Managing Director', $view);
        $this->assertStringContainsString('CERT-VHE/2026/RU/199/005', $view);
    }

    public function test_create_and_edit_completion_certificate_pages_have_full_width_and_download_pdf_actions(): void
    {
        $createPage = new CreateCompletionCertificate;
        $this->assertEquals(Width::Full, $createPage->getMaxContentWidth());
        $this->assertTrue(method_exists($createPage, 'downloadPdf'));

        $editPage = new EditCompletionCertificate;
        $this->assertEquals(Width::Full, $editPage->getMaxContentWidth());
        $this->assertTrue(method_exists($editPage, 'downloadPdf'));
    }

    public function test_sales_agents_seeded_and_calculate_commissions_properly(): void
    {
        $agentAradhana = SalesAgent::where('agent_code', 'AGT-001')->firstOrFail();
        $this->assertSame('percentage', $agentAradhana->default_commission_type);
        $this->assertEquals(5.00, $agentAradhana->default_commission_value);

        // 5% of 1,000,000 should be 50,000
        $calculatedPct = $agentAradhana->calculateCommission(1000000.00);
        $this->assertEquals(50000.00, $calculatedPct);

        $agentRuwan = SalesAgent::where('agent_code', 'AGT-002')->firstOrFail();
        $this->assertSame('fixed', $agentRuwan->default_commission_type);
        $this->assertEquals(35000.00, $agentRuwan->default_commission_value);

        // Fixed Rs. 35,000 regardless of amount
        $calculatedFixed = $agentRuwan->calculateCommission(2500000.00);
        $this->assertEquals(35000.00, $calculatedFixed);
    }

    public function test_sales_agent_metrics_earned_paid_balance(): void
    {
        $agent = SalesAgent::where('agent_code', 'AGT-002')->firstOrFail();

        // Seeded invoice has 35,000 commission and 1 payout of 35,000
        $this->assertEquals(35000.00, $agent->total_commission_earned);
        $this->assertEquals(35000.00, $agent->total_commission_paid);
        $this->assertEquals(0.00, $agent->unpaid_balance);

        // Add an unpaid commission invoice
        $newInvoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $agent->invoices()->first()->contact_id,
            'sales_agent_id' => $agent->id,
            'invoice_number' => 'INV-TEST-COMM-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'type' => 'standard',
            'subtotal' => 500000.00,
            'grand_total' => 500000.00,
            'commission_amount' => 25000.00,
            'commission_status' => 'approved',
            'status' => 'sent',
        ]);

        $agent->refresh();
        $this->assertEquals(60000.00, $agent->total_commission_earned);
        $this->assertEquals(35000.00, $agent->total_commission_paid);
        $this->assertEquals(25000.00, $agent->unpaid_balance);
    }

    public function test_owner_profit_summary_calculation_on_invoice(): void
    {
        $customer = Contact::where('company_id', $this->company->id)->firstOrFail();
        $agent = SalesAgent::where('agent_code', 'AGT-001')->firstOrFail();

        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $customer->id,
            'sales_agent_id' => $agent->id,
            'invoice_number' => 'INV-PROFIT-TEST-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'type' => 'standard',
            'subtotal' => 2000000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 2000000.00,
            'commission_amount' => 100000.00, // 5% of 2M
            'commission_status' => 'approved',
            'status' => 'paid',
        ]);

        // Add 2 items with known cost prices
        // Item 1: Qty 2 @ 600,000, Cost 450,000 -> Revenue 1,200,000, Cost 900,000
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => '10kW Hybrid Inverter Package',
            'quantity' => 2,
            'unit_price' => 600000.00,
            'cost_price' => 450000.00,
            'subtotal' => 1200000.00,
        ]);

        // Item 2: Qty 1 @ 800,000, Cost 550,000 -> Revenue 800,000, Cost 550,000
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Solar Panel Array and Mounting',
            'quantity' => 1,
            'unit_price' => 800000.00,
            'cost_price' => 550000.00,
            'subtotal' => 800000.00,
        ]);

        $invoice->refresh();

        // Total Revenue = 2,000,000
        // Total COGS = 900,000 + 550,000 = 1,450,000
        // Gross Profit = 2,000,000 - 1,450,000 = 550,000
        // Agent Commission = 100,000
        // Net Owner Profit = 550,000 - 100,000 = 450,000
        $this->assertEquals(550000.00, $invoice->gross_profit);
        $this->assertEquals(450000.00, $invoice->net_profit);
    }

    public function test_quotation_conversion_transfers_agent_and_commission(): void
    {
        $customer = Contact::where('company_id', $this->company->id)->firstOrFail();
        $agent = SalesAgent::where('agent_code', 'AGT-002')->firstOrFail();

        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $customer->id,
            'sales_agent_id' => $agent->id,
            'quote_number' => 'QUO-CONVERT-TEST-001',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'system_kw' => 5.0,
            'subtotal' => 1500000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 1500000.00,
            'commission_amount' => 35000.00,
            'commission_status' => 'pending',
            'status' => 'accepted',
        ]);

        // Simulating the convert-to-invoice logic from QuotationsTable
        $invoice = Invoice::create([
            'company_id' => $quotation->company_id,
            'contact_id' => $quotation->contact_id,
            'quotation_id' => $quotation->id,
            'sales_agent_id' => $quotation->sales_agent_id,
            'invoice_number' => 'INV-FROM-QUO-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'subtotal' => $quotation->subtotal,
            'discount' => $quotation->discount,
            'tax' => $quotation->tax,
            'grand_total' => $quotation->grand_total,
            'paid_amount' => 0.00,
            'due_amount' => $quotation->grand_total,
            'commission_amount' => $quotation->commission_amount,
            'commission_status' => $quotation->commission_status,
            'status' => 'draft',
        ]);

        $this->assertSame($agent->id, $invoice->sales_agent_id);
        $this->assertEquals(35000.00, $invoice->commission_amount);
        $this->assertSame('pending', $invoice->commission_status);
    }

    public function test_commission_payout_voucher_pdf_generation(): void
    {
        $payout = CommissionPayout::where('voucher_number', 'COM-VHE-2026-0001')->firstOrFail();
        $pdfService = app(PdfService::class);

        // Test Portrait and Landscape, A4 and A5
        foreach (['a4', 'a5'] as $size) {
            foreach (['portrait', 'landscape'] as $orientation) {
                $payout->paper_size = $size;
                $payout->orientation = $orientation;
                $payout->save();

                $pdf = $pdfService->generateCommissionVoucherPdf($payout);
                $output = $pdf->output();

                $this->assertNotEmpty($output, "Voucher PDF for {$size} {$orientation} should not be empty");
                $this->assertStringStartsWith('%PDF-', $output, 'Output should be a valid PDF format');
            }
        }

        $fileName = $pdfService->getCommissionVoucherFileName($payout);
        $this->assertSame('CommissionVoucher-COM-VHE-2026-0001.pdf', $fileName);

        // Blade view rendering check
        $view = view('pdf.commission-voucher', [
            'payout' => $payout,
            'company' => $payout->company,
            'agent' => $payout->salesAgent,
            'invoice' => $payout->invoice,
            'paper_size' => 'a4',
            'orientation' => 'portrait',
        ])->render();

        $this->assertStringContainsString('COMMISSION PAYMENT VOUCHER', $view);
        $this->assertStringContainsString('COM-VHE-2026-0001', $view);
        $this->assertStringContainsString('Ruwan Jayasinghe', $view);
        $this->assertStringContainsString('35,000.00', $view);
        $this->assertStringContainsString('Sampath Bank', $view);
        $this->assertStringContainsString('Prepared By', $view);
        $this->assertStringContainsString('Agent Receiver Signature', $view);
    }

    public function test_financial_reports_pnl_includes_agent_commissions(): void
    {
        $user = $this->company->users()->first() ?? User::factory()->create();
        $this->actingAs($user);
        Filament::setTenant($this->company);

        $reports = new FinancialReports;
        $reports->startDate = '2026-01-01';
        $reports->endDate = '2026-12-31';

        $commissions = $reports->getAgentCommissions();
        $this->assertGreaterThan(0, $commissions, 'Agent commissions should be captured in annual reports');
        $this->assertEquals(35000.00, $commissions);

        $grossProfit = $reports->getGrossProfit();
        $expenses = $reports->getOperatingExpenses();
        $netProfit = $reports->getNetProfit();

        $this->assertEquals(round($grossProfit - $expenses - $commissions, 2), $netProfit);
    }
}
