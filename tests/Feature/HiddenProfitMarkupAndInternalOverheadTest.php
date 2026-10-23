<?php

namespace Tests\Feature;

use App\Filament\App\Pages\FinancialReports;
use App\Models\Category;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use App\Services\DocumentItemConsolidator;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HiddenProfitMarkupAndInternalOverheadTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $owner;

    protected Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->company = Company::where('slug', 'lanka-solar-solutions')->firstOrFail();

        $this->owner = User::firstOrCreate(
            ['email' => 'owner_hidden_profit_test@lankasolar.lk'],
            ['name' => 'Test Owner', 'password' => Hash::make('Password@123'), 'is_active' => true]
        );

        if (! $this->company->users()->where('user_id', $this->owner->id)->exists()) {
            $this->company->users()->attach($this->owner->id, ['role' => 'owner']);
        }

        $this->actingAs($this->owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);

        $this->contact = Contact::firstOrCreate(
            ['company_id' => $this->company->id, 'email' => 'client.gamini@example.com'],
            ['name' => 'Mr. Gamini Test Client', 'phone' => '0779998877']
        );
    }

    public function test_quotation_with_profit_markup_increases_grand_total_correctly(): void
    {
        // Subtotal: 1,000,000 | Discount: 20,000 | Tax: 0 | Markup: 50,000
        // Expected Grand Total = 1,000,000 - 20,000 + 0 + 50,000 = 1,030,000.00
        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-TST-001',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => 1000000.00,
            'discount' => 20000.00,
            'tax' => 0.00,
            'profit_markup' => 50000.00,
            'profit_markup_type' => 'fixed',
            'grand_total' => 1030000.00,
            'status' => 'draft',
            'price_display_mode' => 'itemized',
            'pdf_item_summary_mode' => 'consolidated',
        ]);

        $this->assertEquals(50000.00, (float) $quotation->profit_markup);
        $this->assertEquals(1030000.00, (float) $quotation->grand_total);
        $this->assertEquals(
            1030000.00,
            (float) $quotation->subtotal - (float) $quotation->discount + (float) $quotation->tax + (float) $quotation->profit_markup
        );
    }

    public function test_invoice_with_profit_markup_increases_grand_total_correctly(): void
    {
        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-TST-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 800000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'profit_markup' => 75000.00,
            'profit_markup_type' => 'fixed',
            'grand_total' => 875000.00,
            'paid_amount' => 0.00,
            'due_amount' => 875000.00,
            'status' => 'unpaid',
            'price_display_mode' => 'itemized',
            'pdf_item_summary_mode' => 'consolidated',
        ]);

        $this->assertEquals(75000.00, (float) $invoice->profit_markup);
        $this->assertEquals(875000.00, (float) $invoice->grand_total);
    }

    public function test_customer_pdf_masks_profit_markup_and_never_mentions_profit_or_markup(): void
    {
        $panelCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Solar PV Panels'], ['slug' => 'cat-panels', 'type' => 'product']);
        $invCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Inverters'], ['slug' => 'cat-inverters', 'type' => 'product']);
        $accCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Mounting & Accessories'], ['slug' => 'cat-acc', 'type' => 'product']);
        $srvCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Installation Services'], ['slug' => 'cat-srv', 'type' => 'service']);

        $p1 = Product::create(['company_id' => $this->company->id, 'category_id' => $panelCat->id, 'name' => 'AE 630W Bifacial Solar PV Panels', 'selling_price' => 38000.00]);
        $p2 = Product::create(['company_id' => $this->company->id, 'category_id' => $invCat->id, 'name' => '5kW Pure Sine Wave Hybrid Solar Inverter', 'selling_price' => 195000.00]);
        $p3 = Product::create(['company_id' => $this->company->id, 'category_id' => $accCat->id, 'name' => 'DC SPD & Enclosure Box', 'selling_price' => 12000.00]);
        $p4 = Product::create(['company_id' => $this->company->id, 'category_id' => $srvCat->id, 'name' => 'Turnkey Electrical Installation & Labour', 'selling_price' => 85000.00]);

        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-HIDDEN-001',
            'quote_date' => '2026-10-23',
            'valid_until' => '2026-11-06',
            'subtotal' => 596000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'profit_markup' => 50000.00,
            'profit_markup_type' => 'fixed',
            'grand_total' => 646000.00,
            'status' => 'draft',
            'price_display_mode' => 'itemized',
            'pdf_item_summary_mode' => 'consolidated',
        ]);

        $quotation->items()->create(['product_id' => $p1->id, 'description' => $p1->name, 'quantity' => 8, 'unit_price' => 38000.00, 'subtotal' => 304000.00]);
        $quotation->items()->create(['product_id' => $p2->id, 'description' => $p2->name, 'quantity' => 1, 'unit_price' => 195000.00, 'subtotal' => 195000.00]);
        $quotation->items()->create(['product_id' => $p3->id, 'description' => $p3->name, 'quantity' => 1, 'unit_price' => 12000.00, 'subtotal' => 12000.00]);
        $quotation->items()->create(['product_id' => $p4->id, 'description' => $p4->name, 'quantity' => 1, 'unit_price' => 85000.00, 'subtotal' => 85000.00]);

        // 1. Verify DocumentItemConsolidator seamlessly absorbs markup into Line 4 (accessories) or Line 5 (services)
        $items = $quotation->items()->with(['product.category'])->get();
        $consolidated = DocumentItemConsolidator::prepareItemsForDisplay($items, consolidate: true, packageMode: false, profitMarkup: 50000.00);

        // Find the accessories line where profit markup was absorbed
        $lineWithAbsorption = collect($consolidated)->first(function ($line) {
            return str_contains($line->product->name, 'Solar Mounting Structures') || str_contains($line->product->name, 'Turnkey Electrical Installation');
        });

        $this->assertNotNull($lineWithAbsorption, 'A consolidated BOS/Services line must be present to absorb profit markup');
        // Original accessories subtotal was 12,000. With 50,000 absorbed markup, it should be 62,000.00
        $this->assertEquals(62000.00, (float) $lineWithAbsorption->subtotal);

        // 2. Render Blade template directly and assert NO "Profit" or "Markup" is visible to the customer
        $html = view('pdf.quotation', [
            'record' => $quotation,
            'quotation' => $quotation,
            'company' => $this->company,
            'priceDisplayMode' => 'itemized',
            'pdfItemSummaryMode' => 'consolidated',
            'primaryColor' => '#0f766e',
        ])->render();

        $this->assertStringNotContainsStringIgnoringCase('profit', $html);
        $this->assertStringNotContainsStringIgnoringCase('markup', $html);

        // Subtotal shown on PDF must match absorbed line items total (596,000 + 50,000 = 646,000.00)
        $this->assertStringContainsString('646,000.00', $html);

        // 3. Render DomPDF and ensure it strictly fits on 1 A4 page
        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateQuotationPdf($quotation, 'itemized', 'consolidated');
        $pdf->render();
        $pageCount = $pdf->getDomPDF()->getCanvas()->get_page_count();

        $this->assertEquals(1, $pageCount, "Consolidated PDF with hidden markup must strictly fit on 1 A4 page (actual: {$pageCount})");
    }

    public function test_customer_invoice_pdf_masks_profit_markup_and_maintains_single_page(): void
    {
        $panelCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Solar PV Panels'], ['slug' => 'cat-panels', 'type' => 'product']);
        $p1 = Product::create(['company_id' => $this->company->id, 'category_id' => $panelCat->id, 'name' => 'AE 630W Bifacial Solar PV Panels', 'selling_price' => 500000.00]);

        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-HIDDEN-001',
            'invoice_date' => '2026-10-23',
            'due_date' => '2026-10-30',
            'subtotal' => 500000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'profit_markup' => 50000.00,
            'grand_total' => 550000.00,
            'paid_amount' => 550000.00,
            'due_amount' => 0.00,
            'status' => 'paid',
            'price_display_mode' => 'package_total_only',
            'pdf_item_summary_mode' => 'consolidated',
        ]);

        $invoice->items()->create(['product_id' => $p1->id, 'description' => $p1->name, 'quantity' => 1, 'unit_price' => 500000.00, 'subtotal' => 500000.00]);

        $html = view('pdf.invoice', [
            'invoice' => $invoice,
            'record' => $invoice,
            'company' => $this->company,
            'priceDisplayMode' => 'package_total_only',
            'pdfItemSummaryMode' => 'consolidated',
            'primaryColor' => '#0f766e',
        ])->render();

        $this->assertStringNotContainsStringIgnoringCase('profit', $html);
        $this->assertStringNotContainsStringIgnoringCase('markup', $html);
        $this->assertStringContainsString('550,000.00', $html);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateInvoicePdf($invoice, 'package_total_only', 'consolidated');
        $pdf->render();
        $pageCount = $pdf->getDomPDF()->getCanvas()->get_page_count();

        $this->assertEquals(1, $pageCount, "Consolidated Invoice PDF must strictly fit on 1 A4 page (actual: {$pageCount})");
    }

    public function test_financial_reports_treat_markup_as_100_percent_profit_and_absorb_internal_overhead(): void
    {
        FinancialReports::flushCacheForTenant($this->company->id);

        $panelCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Solar PV Panels'], ['slug' => 'cat-panels-fin', 'type' => 'product']);
        $prod = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $panelCat->id,
            'name' => 'Solar Panels Batch',
            'selling_price' => 1000000.00,
            'cost_price' => 600000.00,
        ]);

        // 1. Create Invoice with subtotal 1,000,000, profit markup 100,000 => Grand Total 1,100,000
        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-FIN-REPORT-TEST-001',
            'invoice_date' => '2026-10-23',
            'due_date' => '2026-10-30',
            'subtotal' => 1000000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'profit_markup' => 100000.00,
            'grand_total' => 1100000.00,
            'commission_amount' => 50000.00,
            'paid_amount' => 1100000.00,
            'due_amount' => 0.00,
            'status' => 'paid',
        ]);

        $invoice->items()->create([
            'product_id' => $prod->id,
            'description' => $prod->name,
            'quantity' => 1,
            'unit_price' => 1000000.00,
            'cost_price' => 600000.00,
            'subtotal' => 1000000.00,
        ]);

        // 2. Add an External Vendor Expense: Rs. 150,000 (Installation Labour)
        Expense::create([
            'company_id' => $this->company->id,
            'invoice_id' => $invoice->id,
            'title' => 'Electrical Installation Labour Outflow',
            'category' => 'Installation Labour',
            'amount' => 150000.00,
            'expense_date' => '2026-10-23',
            'is_internal_overhead' => false,
        ]);

        // 3. Add an Internal Company Overhead Expense: Rs. 112,000 (Office Overhead Absorption)
        Expense::create([
            'company_id' => $this->company->id,
            'invoice_id' => $invoice->id,
            'title' => 'Head Office Space & Operational Overhead Absorption',
            'category' => 'Office Rent & Overhead',
            'amount' => 112000.00,
            'expense_date' => '2026-10-23',
            'is_internal_overhead' => true,
        ]);

        $reports = new FinancialReports;
        $reports->selectedInvoiceId = $invoice->id;
        $metrics = $reports->getInvoiceProfitabilityMetrics();

        $this->assertTrue($metrics['has_data']);
        $this->assertEquals(1100000.00, $metrics['contract_value']);

        // Section A: Direct External Vendor Outflow
        // Materials (600,000 from items cost_price fallback) + Labour (150,000) + Commission (50,000) = 800,000.00
        $this->assertEquals(800000.00, $metrics['direct_external_outflow']);

        // Section B: Internal Company Overhead Allocation = 112,000.00
        $this->assertEquals(112000.00, $metrics['internal_company_overhead']);

        // Company Retained Margin = Contract Revenue (1,100,000) - Direct External Outflow (800,000) = 300,000.00
        // (Keeps Rs. 112,000 inside the company's gross profit pool!)
        $this->assertEquals(300000.00, $metrics['company_retained_margin']);
        $this->assertEquals(27.3, $metrics['retained_margin_percentage']);

        // Total Combined Cost = 800,000 + 112,000 = 912,000.00
        $this->assertEquals(912000.00, $metrics['total_project_cost']);

        // Period Financial Statement Net Profit must NOT deduct internal overhead (112,000) from bottom-line retained earnings:
        $finMetrics = $reports->getFinancialMetrics();
        $this->assertGreaterThanOrEqual(112000.00, $finMetrics['internal_overhead']);
        $this->assertGreaterThanOrEqual(150000.00, $finMetrics['external_expenses']);

        // Invoiced Sales includes the markup: 1,100,000
        $this->assertGreaterThanOrEqual(1100000.00, $finMetrics['invoiced_sales']);
        $this->assertGreaterThanOrEqual(100000.00, $finMetrics['total_profit_markup']);

        // Bottom-line Net Profit = Invoiced Sales - COGS - External Expenses - Commissions
        // Note that internal overhead is NOT deducted!
        $this->assertGreaterThanOrEqual(
            $finMetrics['invoiced_sales'] - $finMetrics['cogs'] - $finMetrics['external_expenses'] - $finMetrics['agent_commissions'],
            $finMetrics['net_profit']
        );
    }
}
