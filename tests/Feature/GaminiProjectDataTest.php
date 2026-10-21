<?php

namespace Tests\Feature;

use App\Models\CommissionPayout;
use App\Models\Company;
use App\Models\CompletionCertificate;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Quotation;
use App\Models\SalesAgent;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\GaminiProjectDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GaminiProjectDataTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->seed(GaminiProjectDataSeeder::class);

        $this->company = Company::where('slug', 'vertex-homes')->first()
            ?? Company::firstOrFail();
    }

    public function test_gamini_suppliers_are_seeded_correctly(): void
    {
        $shreeEnergy = Contact::where('company_id', $this->company->id)
            ->where('name', 'Shree Energy (Pvt) Ltd')
            ->first();
        $this->assertNotNull($shreeEnergy, 'Shree Energy (Pvt) Ltd should exist as a supplier');
        $this->assertSame('supplier', $shreeEnergy->type);
        $this->assertStringContainsString('Wallampitiya', $shreeEnergy->address);
        $this->assertStringContainsString('0760332537', $shreeEnergy->phone);

        $asianElectronics = Contact::where('company_id', $this->company->id)
            ->where('name', 'Asian Electronics & Electrical')
            ->first();
        $this->assertNotNull($asianElectronics, 'Asian Electronics & Electrical should exist as a supplier');
        $this->assertSame('supplier', $asianElectronics->type);
        $this->assertStringContainsString('Mirigama', $asianElectronics->address);
        $this->assertStringContainsString('0724365237', $asianElectronics->phone);

        $victorines = Contact::where('company_id', $this->company->id)
            ->where('name', 'Victorines')
            ->first();
        $this->assertNotNull($victorines, 'Victorines should exist as a supplier');
        $this->assertSame('supplier', $victorines->type);
        $this->assertStringContainsString('Ja-Ela', $victorines->address);
        $this->assertSame('0716503737', $victorines->phone);

        $ajantha = Contact::where('company_id', $this->company->id)
            ->where('name', 'Ajantha Hardware')
            ->first();
        $this->assertNotNull($ajantha, 'Ajantha Hardware should exist as a supplier');
        $this->assertSame('supplier', $ajantha->type);
        $this->assertStringContainsString('Mirigama', $ajantha->address);
        $this->assertSame('033 2273362', $ajantha->phone);

        // Additional suppliers referenced in bills
        $this->assertNotNull(Contact::where('company_id', $this->company->id)->where('name', 'Fortune Green (Pvt) Ltd')->first());
        $this->assertNotNull(Contact::where('company_id', $this->company->id)->where('name', 'Vertex Store')->first());
        $this->assertNotNull(Contact::where('company_id', $this->company->id)->where('name', 'Anthony Hardware')->first());
        $this->assertNotNull(Contact::where('company_id', $this->company->id)->where('name', 'Electrical Supplier')->first());
        $this->assertNotNull(Contact::where('company_id', $this->company->id)->where('name', 'Sundry Site Materials')->first());
    }

    public function test_gamini_products_and_cost_prices_are_seeded(): void
    {
        $expectedProducts = [
            'INV-SUMRY-10.2KW-OFFGRID' => 225000.00,
            'BAT-LIFEPO4-51.2V-314AH' => 680000.00,
            'SW-8WAY-BOX-IP65' => 2900.00,
            'PANEL-JINKO-640W-BIFACIAL' => 45021.25,
            'MNT-BOS-10.2KW-SET' => 131380.00,
            'SRV-TURNKEY-INSTALL-10.2KW' => 197000.00,
        ];

        foreach ($expectedProducts as $sku => $costPrice) {
            $product = Product::where('company_id', $this->company->id)
                ->where('sku', $sku)
                ->first();

            $this->assertNotNull($product, "Product with SKU {$sku} should be seeded");
            $this->assertEquals($costPrice, (float) $product->cost_price, "Product {$sku} cost price should match");
        }
    }

    public function test_gamini_inverter_serial_number_registration(): void
    {
        $serial = ProductSerial::where('company_id', $this->company->id)
            ->where('serial_number', 'SN 9201232882604140992')
            ->first();

        $this->assertNotNull($serial, 'Inverter serial number SN 9201232882604140992 should be registered');
        $this->assertSame(60, $serial->warranty_months, 'Sumry Inverter should have 5 years (60 months) warranty');
        $this->assertSame('sold', $serial->status);

        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'VHE/2026/00179')
            ->firstOrFail();

        $customer = Contact::where('company_id', $this->company->id)
            ->where('name', 'Mr. Gamini')
            ->firstOrFail();

        $this->assertSame($invoice->id, $serial->invoice_id);
        $this->assertSame($customer->id, $serial->contact_id);
        $this->assertSame('2031-09-21', $serial->warranty_expires_at->toDateString());
    }

    public function test_gamini_sales_agent_krishantha_and_commission_payout(): void
    {
        $agent = SalesAgent::where('company_id', $this->company->id)
            ->where('name', 'S L R Krishantha')
            ->first();

        $this->assertNotNull($agent, 'Sales agent S L R Krishantha should exist');
        $this->assertSame("People's Bank", $agent->bank_name);
        $this->assertSame('269200168492252', $agent->bank_account_no);
        $this->assertEquals(3.00, (float) $agent->default_commission_value);

        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'VHE/2026/00179')
            ->firstOrFail();

        $this->assertSame($agent->id, $invoice->sales_agent_id);
        $this->assertEquals(57000.00, (float) $invoice->commission_amount, '3% commission on 1,900,000 should be 57,000.00');
        $this->assertSame('paid', $invoice->commission_status);

        $payout = CommissionPayout::where('company_id', $this->company->id)
            ->where('voucher_number', 'COM-VHE-2026-0003')
            ->first();

        $this->assertNotNull($payout, 'Commission payout voucher COM-VHE-2026-0003 should exist');
        $this->assertSame($agent->id, $payout->sales_agent_id);
        $this->assertSame($invoice->id, $payout->invoice_id);
        $this->assertEquals(57000.00, (float) $payout->amount);
        $this->assertSame('paid', $payout->status);
        $this->assertSame('COMM-KRISHANTHA', $payout->reference_no);
    }

    public function test_gamini_customer_quotation_and_invoice(): void
    {
        $customer = Contact::where('company_id', $this->company->id)
            ->where('name', 'Mr. Gamini')
            ->firstOrFail();

        $this->assertSame('customer', $customer->type);
        $this->assertStringContainsString('Mirigama', $customer->address);

        $quotation = Quotation::where('company_id', $this->company->id)
            ->where('quote_number', 'VHE/2026/00179')
            ->firstOrFail();

        $this->assertSame($customer->id, $quotation->contact_id);
        $this->assertEquals(10.20, (float) $quotation->system_kw);
        $this->assertSame('single', $quotation->phase_type);
        $this->assertSame('Jinko 640W', $quotation->panel_brand);
        $this->assertSame('10.2kW Sumry Off-grid', $quotation->inverter_brand);
        $this->assertSame('package_total_only', $quotation->price_display_mode);
        $this->assertEquals(1925000.00, (float) $quotation->subtotal);
        $this->assertEquals(25000.00, (float) $quotation->discount);
        $this->assertEquals(1900000.00, (float) $quotation->grand_total);
        $this->assertSame('accepted', $quotation->status);

        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'VHE/2026/00179')
            ->firstOrFail();

        $this->assertSame($quotation->id, $invoice->quotation_id);
        $this->assertSame($customer->id, $invoice->contact_id);
        $this->assertSame('2026-09-21', $invoice->invoice_date->toDateString());
        $this->assertSame('package_total_only', $invoice->price_display_mode);
        $this->assertEquals(1925000.00, (float) $invoice->subtotal);
        $this->assertEquals(25000.00, (float) $invoice->discount);
        $this->assertEquals(1900000.00, (float) $invoice->grand_total);
        $this->assertEquals(1900000.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->due_amount);
        $this->assertSame('paid', $invoice->status);
    }

    public function test_gamini_customer_3_actual_payments(): void
    {
        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'VHE/2026/00179')
            ->firstOrFail();

        $payments = Payment::where('invoice_id', $invoice->id)->orderBy('payment_date')->get();

        $this->assertCount(3, $payments, 'Exactly 3 actual payments should be recorded');

        // Payment 1: 2026-09-21 | 900,000.00
        $this->assertEquals(900000.00, (float) $payments[0]->amount);
        $this->assertSame('2026-09-21', $payments[0]->payment_date->toDateString());

        // Payment 2: 2026-09-23 | 525,000.00
        $this->assertEquals(525000.00, (float) $payments[1]->amount);
        $this->assertSame('2026-09-23', $payments[1]->payment_date->toDateString());

        // Payment 3: 2026-09-26 | 475,000.00
        $this->assertEquals(475000.00, (float) $payments[2]->amount);
        $this->assertSame('2026-09-26', $payments[2]->payment_date->toDateString());

        $this->assertEquals(1900000.00, (float) $payments->sum('amount'));
        $this->assertEquals(1900000.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->due_amount);
        $this->assertSame('paid', $invoice->status);
    }

    public function test_gamini_exact_project_costing_and_net_owner_profit_of_159407_50(): void
    {
        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'VHE/2026/00179')
            ->with('items')
            ->firstOrFail();

        $this->assertCount(5, $invoice->items, 'All 5 customer solar package items should be present');

        // Revenue (Grand Total)
        $this->assertEquals(1900000.00, (float) $invoice->grand_total);

        // COGS (Items Total Cost) = 1,683,592.50
        $this->assertEquals(1683592.50, (float) $invoice->total_cost, 'Total Cost (COGS) must equal 1,683,592.50');

        // Gross Profit = 1,900,000.00 - 1,683,592.50 = 216,407.50
        $this->assertEquals(216407.50, (float) $invoice->gross_profit, 'Gross Profit must equal 216,407.50');

        // Agent Commission = 57,000.00
        $this->assertEquals(57000.00, (float) $invoice->commission_amount, 'Agent Commission must equal 57,000.00');

        // Total Project Expenditure (Items Cost + Commission) = 1,740,592.50
        $totalExpenditure = $invoice->total_cost + (float) $invoice->commission_amount;
        $this->assertEquals(1740592.50, $totalExpenditure, 'Total Project Expenditure must equal 1,740,592.50');

        // Net Owner Profit = 1,900,000.00 - 1,740,592.50 = 159,407.50
        $this->assertEquals(159407.50, (float) $invoice->net_profit, 'Net Owner Profit must equal exactly 159,407.50');

        // Profit Margin % = 8.39%
        $this->assertEquals(8.39, (float) $invoice->profit_margin_percentage, 'Net Profit Margin should be 8.39%');
    }

    public function test_gamini_all_9_real_world_supplier_expenses_and_3_operational_expenses(): void
    {
        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'VHE/2026/00179')
            ->firstOrFail();

        $expenses = Expense::where('invoice_id', $invoice->id)->get();
        $this->assertCount(12, $expenses, 'There should be 12 total expenses (9 materials bills + 3 operational expenses)');

        // 9 Materials Bills = LKR 1,486,592.50
        $materialsRefs = [
            'VH-FG-20263606' => 680000.00,
            '202604146' => 225000.00,
            'CASH-SALE-004' => 450212.50,
            'CASH-SALE-005' => 66160.00,
            'CASH-SALE-006' => 38200.00,
            '7951497' => 17420.00,
            'ASIAN-220926' => 5800.00,
            'AJANTHA-220926' => 1800.00,
            'SITE-SUNDRY' => 2000.00,
        ];

        $materialsSum = 0.0;
        foreach ($materialsRefs as $ref => $expectedAmount) {
            $exp = $expenses->firstWhere('reference_no', $ref);
            $this->assertNotNull($exp, "Expense with reference {$ref} must exist");
            $this->assertEquals($expectedAmount, (float) $exp->amount, "Expense {$ref} amount must match");
            $materialsSum += (float) $exp->amount;
        }
        $this->assertEquals(1486592.50, $materialsSum, 'Total Materials Expenses must equal LKR 1,486,592.50');

        // 3 Operational Expenses = LKR 254,000.00
        $operationalRefs = [
            'LABOUR-GAMINI' => 85000.00,
            'OFFICE-EXP-GAMINI' => 112000.00,
            'COMM-KRISHANTHA' => 57000.00,
        ];

        $operationalSum = 0.0;
        foreach ($operationalRefs as $ref => $expectedAmount) {
            $exp = $expenses->firstWhere('reference_no', $ref);
            $this->assertNotNull($exp, "Expense with reference {$ref} must exist");
            $this->assertEquals($expectedAmount, (float) $exp->amount, "Expense {$ref} amount must match");
            $operationalSum += (float) $exp->amount;
        }
        $this->assertEquals(254000.00, $operationalSum, 'Total Operational Expenses must equal LKR 254,000.00');

        // Combined Total Expenses = 1,740,592.50
        $this->assertEquals(1740592.50, $materialsSum + $operationalSum, 'Total Combined Project Expenses must equal LKR 1,740,592.50');

        // Net Owner Profit check against expenses:
        $netOwnerProfit = (float) $invoice->grand_total - ($materialsSum + $operationalSum);
        $this->assertEquals(159407.50, $netOwnerProfit, 'Revenue minus total expenses must equal LKR 159,407.50');
    }

    public function test_gamini_completion_certificate_is_seeded(): void
    {
        $cert = CompletionCertificate::where('company_id', $this->company->id)
            ->where('certificate_number', 'CERT-VHE/2026/00179')
            ->first();

        $this->assertNotNull($cert, 'Completion certificate CERT-VHE/2026/00179 should exist');
        $this->assertSame('Mr. Gamini', $cert->customer_name);
        $this->assertStringContainsString('10.20 kW', $cert->system_capacity);
        $this->assertStringContainsString('16kWh', $cert->system_capacity);
        $this->assertSame('2026-09-26', $cert->commissioning_date->toDateString());
    }

    public function test_gamini_solar_pdf_generation_works_cleanly(): void
    {
        $quotation = Quotation::where('company_id', $this->company->id)
            ->where('quote_number', 'VHE/2026/00179')
            ->firstOrFail();

        $invoice = Invoice::where('company_id', $this->company->id)
            ->where('invoice_number', 'VHE/2026/00179')
            ->firstOrFail();

        $pdfService = app(PdfService::class);

        // Test Quotation PDF
        $pdfQuote = $pdfService->generateSolarAgreementPdf($quotation);
        $this->assertNotEmpty($pdfQuote->output());

        // Test Invoice PDF
        $pdfInv = $pdfService->generateSolarAgreementPdf($invoice);
        $this->assertNotEmpty($pdfInv->output());
    }
}
