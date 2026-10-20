<?php

namespace Tests\Feature;

use App\Models\CommissionPayout;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SalesAgent;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\VertexHomesProjectDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VertexHomesProjectDataTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->seed(VertexHomesProjectDataSeeder::class);

        $this->company = Company::where('slug', 'vertex-homes')->first()
            ?? Company::firstOrFail();
    }

    public function test_suppliers_are_seeded_correctly(): void
    {
        $fortuneGreen = Contact::where('company_id', $this->company->id)
            ->where('name', 'Fortune Green (Pvt) Ltd')
            ->first();

        $this->assertNotNull($fortuneGreen, 'Fortune Green (Pvt) Ltd should exist as a supplier');
        $this->assertSame('supplier', $fortuneGreen->type);
        $this->assertStringContainsString('Kelaniya', $fortuneGreen->address);

        $geyli = Contact::where('company_id', $this->company->id)
            ->where('name', 'Geyli Hardware')
            ->first();

        $this->assertNotNull($geyli, 'Geyli Hardware should exist as a supplier');
        $this->assertSame('supplier', $geyli->type);
        $this->assertStringContainsString('Loluwagoda', $geyli->address);

        $sampath = Contact::where('company_id', $this->company->id)
            ->where('name', 'New Sampath Electricals')
            ->first();

        $this->assertNotNull($sampath, 'New Sampath Electricals should exist as a supplier');
        $this->assertSame('supplier', $sampath->type);
    }

    public function test_products_and_exact_cost_prices_are_seeded(): void
    {
        $expectedProducts = [
            'PANEL-AE-630W-BIFACIAL' => 29237.29,
            'BAT-LIFEPO4-51.2V-100AH' => 296610.17,
            'INV-OFFGRID-5KW-48V' => 146489.00,
            'MNT-D-RAIL-04' => 5762.72,
            'SW-DC-BREAKER-BB2-40' => 4788.14,
            'SW-DC-SPD-BUD-40-2R' => 5254.24,
            'SW-AC-SPD-BUA-40-2' => 2881.36,
            'CAB-LAPP-4MM-DC' => 16788.80,
            'SW-EARTH-ROD-5FT' => 3000.00,
            'SW-ACL-MAIN-SWITCH-40A' => 2100.00,
        ];

        foreach ($expectedProducts as $sku => $costPrice) {
            $product = Product::where('company_id', $this->company->id)
                ->where('sku', $sku)
                ->first();

            $this->assertNotNull($product, "Product with SKU {$sku} should be seeded");
            $this->assertEquals($costPrice, (float) $product->cost_price, "Product {$sku} cost price should match exact document");
        }
    }

    public function test_sales_agent_krishantha_and_commission_payout_are_seeded(): void
    {
        $agent = SalesAgent::where('company_id', $this->company->id)
            ->where('agent_code', 'AGT-003')
            ->first();

        $this->assertNotNull($agent, 'Sales agent Krishantha (AGT-003) should be seeded');
        $this->assertSame('Krishantha', $agent->name);
        $this->assertEquals(3.00, (float) $agent->default_commission_value);
        $this->assertSame('percentage', $agent->default_commission_type);

        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'INV-2026-0005')
            ->firstOrFail();

        $this->assertSame($agent->id, $invoice->sales_agent_id);
        $this->assertEquals(37500.00, (float) $invoice->commission_amount, '3% commission on 1,250,000 should be 37,500.00');
        $this->assertSame('paid', $invoice->commission_status);

        $payout = CommissionPayout::where('voucher_number', 'COM-VHE-2026-0002')->first();
        $this->assertNotNull($payout, 'Commission payout voucher COM-VHE-2026-0002 should exist');
        $this->assertSame($agent->id, $payout->sales_agent_id);
        $this->assertSame($invoice->id, $payout->invoice_id);
        $this->assertEquals(37500.00, (float) $payout->amount);
        $this->assertSame('paid', $payout->status);
    }

    public function test_customer_milestone_payments_and_paid_status(): void
    {
        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'INV-2026-0005')
            ->firstOrFail();

        $payments = Payment::where('invoice_id', $invoice->id)->orderBy('payment_date')->get();

        $this->assertCount(2, $payments, 'Exactly 2 milestone payments should be recorded');
        $this->assertEquals(625000.00, (float) $payments[0]->amount);
        $this->assertSame('2026-10-15', $payments[0]->payment_date->toDateString());
        $this->assertEquals(625000.00, (float) $payments[1]->amount);
        $this->assertSame('2026-10-18', $payments[1]->payment_date->toDateString());

        $this->assertEquals(1250000.00, (float) $invoice->grand_total);
        $this->assertEquals(1250000.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->due_amount);
        $this->assertSame('paid', $invoice->status);
    }

    public function test_project_costing_card_and_exact_net_owner_profit_of_209701(): void
    {
        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'INV-2026-0005')
            ->with('items')
            ->firstOrFail();

        $this->assertCount(12, $invoice->items, 'All 12 supplier and operational items should be linked');

        // Revenue
        $this->assertEquals(1250000.00, (float) $invoice->grand_total);

        // COGS (Items Total Cost) = 1,002,799.00
        $this->assertEquals(1002799.00, (float) $invoice->total_cost, 'Total Cost (COGS) must equal 1,002,799.00');

        // Gross Profit = 1,250,000.00 - 1,002,799.00 = 247,201.00
        $this->assertEquals(247201.00, (float) $invoice->gross_profit, 'Gross Profit must equal 247,201.00');

        // Agent Commission = 37,500.00
        $this->assertEquals(37500.00, (float) $invoice->commission_amount, 'Agent Commission must equal 37,500.00');

        // Total Project Costs (COGS + Commission) = 1,040,299.00
        $totalProjectExpenses = $invoice->total_cost + (float) $invoice->commission_amount;
        $this->assertEquals(1040299.00, $totalProjectExpenses, 'Total Project Expenditure must equal 1,040,299.00');

        // Net Owner Profit = 1,250,000.00 - 1,040,299.00 = 209,701.00
        $this->assertEquals(209701.00, (float) $invoice->net_profit, 'Net Owner Profit must equal exactly 209,701.00');

        // Profit Margin % = 16.78%
        $this->assertEquals(16.78, (float) $invoice->profit_margin_percentage, 'Net Profit Margin should be 16.78%');
    }

    public function test_solar_system_installation_agreement_pdf_generator(): void
    {
        $quotation = Quotation::where('company_id', $this->company->id)
            ->where('quote_number', 'VHE/2026/RU/00199/005')
            ->firstOrFail();

        $pdfService = app(PdfService::class);

        // Test Quotation PDF Generation
        $pdfQuote = $pdfService->generateSolarAgreementPdf($quotation);
        $outputQuote = $pdfQuote->output();
        $this->assertNotEmpty($outputQuote);
        $this->assertStringStartsWith('%PDF-', $outputQuote);

        $fileName = $pdfService->getSolarAgreementFileName($quotation);
        $this->assertSame('SolarAgreement-VHE-2026-RU-00199-005.pdf', $fileName);
        $this->assertStringNotContainsString('/', $fileName);
        $this->assertStringNotContainsString('\\', $fileName);

        // Test Invoice PDF Generation
        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'INV-2026-0005')
            ->firstOrFail();

        $pdfInv = $pdfService->generateSolarAgreementPdf($invoice);
        $outputInv = $pdfInv->output();
        $this->assertNotEmpty($outputInv);
        $this->assertStringStartsWith('%PDF-', $outputInv);

        // Test Blade Template Renders 2 Pages with all required official elements
        $view = view('pdf.solar-agreement', [
            'record' => $quotation,
            'company' => $quotation->company,
            'contact' => $quotation->contact,
            'agreementNumber' => $quotation->quote_number,
            'quotationNumber' => $quotation->quote_number,
            'agreementDate' => '2026-10-10',
            'agentName' => 'Krishantha (AGT-003)',
        ])->render();

        // Page 1 assertions
        $this->assertStringContainsString('VERTEX HOME SOLUTIONS (PVT) LTD', $view);
        $this->assertStringContainsString('Installation Agreement', $view);
        $this->assertStringContainsString('VHE/2026/RU/00199/005', $view);
        $this->assertStringContainsString('MR. M A N A M Fernando', $view);
        $this->assertStringContainsString('260/20, Posparati Garden, Katana Road, Thimbitigaskatuwa', $view);
        $this->assertStringContainsString('CEB-RU-199-005', $view);
        $this->assertStringContainsString('AE 630W Bifacial', $view);
        $this->assertStringContainsString('LiFePO4 Lithium Iron Phosphate 51.2V 100Ah', $view);
        $this->assertStringContainsString('5.0 kW Off-Grid / Hybrid Smart Solar Inverter', $view);
        $this->assertStringContainsString('1,250,000.00', $view);
        $this->assertStringContainsString('625,000.00', $view);
        $this->assertStringContainsString('101001490231', $view);
        $this->assertStringContainsString('Page 1 of 2', $view);

        // Page 2 assertions
        $this->assertStringContainsString('Scope of Engineering & Installation Services', $view);
        $this->assertStringContainsString('Comprehensive Equipment & Service Warranties', $view);
        $this->assertStringContainsString('30-Year Linear Power Performance Warranty', $view);
        $this->assertStringContainsString('10-Year Comprehensive Manufacturer Warranty', $view);
        $this->assertStringContainsString('Krishantha (AGT-003)', $view);
        $this->assertStringContainsString('Managing Director', $view);
        $this->assertStringContainsString('Page 2 of 2', $view);
    }
}
