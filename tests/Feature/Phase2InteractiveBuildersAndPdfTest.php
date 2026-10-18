<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Invoices\Pages\ListInvoices;
use App\Filament\App\Resources\Quotations\Pages\ListQuotations;
use App\Filament\App\Widgets\AppStatsOverviewWidget;
use App\Filament\App\Widgets\MonthlyRevenueExpenseChartWidget;
use App\Filament\App\Widgets\RecentSolarQuotationsWidget;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class Phase2InteractiveBuildersAndPdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_quotation_has_solar_fields_and_item_taxes(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $customer = Contact::where('company_id', $company->id)->first();
        $product = Product::where('company_id', $company->id)->first();

        $quotation = Quotation::create([
            'company_id' => $company->id,
            'contact_id' => $customer->id,
            'quote_number' => 'QUO-2026-TEST',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'system_kw' => 15.00,
            'phase_type' => 'three',
            'roof_type' => 'Flat Concrete Slab',
            'inverter_brand' => 'Huawei 15kW Three Phase',
            'panel_brand' => 'Jinko Solar 585W Bifacial',
            'subtotal' => 500000.00,
            'discount' => 20000.00,
            'tax' => 10000.00,
            'grand_total' => 490000.00,
            'status' => 'draft',
        ]);

        $item = QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $product->id,
            'description' => '15kW Inverter Unit',
            'quantity' => 1,
            'unit_price' => 500000.00,
            'tax' => 10000.00,
            'subtotal' => 510000.00,
        ]);

        $this->assertEquals(15.00, (float) $quotation->system_kw);
        $this->assertEquals('three', $quotation->phase_type);
        $this->assertEquals('Flat Concrete Slab', $quotation->roof_type);
        $this->assertEquals('Huawei 15kW Three Phase', $quotation->inverter_brand);
        $this->assertEquals('Jinko Solar 585W Bifacial', $quotation->panel_brand);
        $this->assertEquals(10000.00, (float) $item->tax);
        $this->assertEquals(510000.00, (float) $item->subtotal);
    }

    public function test_convert_accepted_quotation_to_invoice_logic(): void
    {
        $quotation = Quotation::where('quote_number', 'QUO-2026-0001')->first();
        $this->assertNotNull($quotation);
        $this->assertEquals('accepted', $quotation->status);
        $this->assertGreaterThan(0, $quotation->items()->count());

        $invoice = DB::transaction(function () use ($quotation) {
            $invoice = Invoice::create([
                'company_id' => $quotation->company_id,
                'contact_id' => $quotation->contact_id,
                'quotation_id' => $quotation->id,
                'invoice_number' => 'INV-2026-CONVERTED',
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'type' => 'milestone',
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'tax' => $quotation->tax,
                'grand_total' => $quotation->grand_total,
                'paid_amount' => 0.00,
                'due_amount' => $quotation->grand_total,
                'status' => 'unpaid',
            ]);

            foreach ($quotation->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax' => $item->tax ?? 0.00,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $quotation->update(['status' => 'invoiced']);

            return $invoice;
        });

        $this->assertNotNull($invoice);
        $this->assertEquals('invoiced', $quotation->fresh()->status);
        $this->assertEquals($quotation->id, $invoice->quotation_id);
        $this->assertEquals($quotation->contact_id, $invoice->contact_id);
        $this->assertEquals((float) $quotation->grand_total, (float) $invoice->grand_total);
        $this->assertEquals((float) $quotation->grand_total, (float) $invoice->due_amount);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertEquals($quotation->items()->count(), $invoice->items()->count());
    }

    public function test_pdf_generation_for_solar_quotation(): void
    {
        $quotation = Quotation::where('quote_number', 'QUO-2026-0001')->first();
        $this->assertNotNull($quotation);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateQuotationPdf($quotation);

        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_pdf_generation_for_solar_invoice(): void
    {
        $invoice = Invoice::where('invoice_number', 'INV-2026-0001')->first();
        $this->assertNotNull($invoice);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateInvoicePdf($invoice);

        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_dashboard_stats_overview_widget(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($user);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $widget = new AppStatsOverviewWidget;
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getStats');
        $method->setAccessible(true);
        $stats = $method->invoke($widget);

        $this->assertCount(4, $stats);
        $labels = array_map(fn ($s) => $s->getLabel(), $stats);
        $this->assertContains('Total Revenue', $labels);
        $this->assertContains('Total Quotations', $labels);
        $this->assertContains('Pending Invoices', $labels);
        $this->assertContains('Low Stock Products', $labels);
    }

    public function test_dashboard_monthly_revenue_expense_chart_widget(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($user);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $widget = new MonthlyRevenueExpenseChartWidget;
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($widget);

        $this->assertArrayHasKey('datasets', $data);
        $this->assertArrayHasKey('labels', $data);
        $this->assertCount(6, $data['labels']);
        $this->assertCount(2, $data['datasets']);
        $this->assertEquals('Revenue / Cash In (Rs.)', $data['datasets'][0]['label']);
        $this->assertEquals('Expenses / Cash Out (Rs.)', $data['datasets'][1]['label']);
    }

    public function test_dashboard_recent_solar_quotations_widget(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($user);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $widget = new RecentSolarQuotationsWidget;
        $this->assertEquals('full', $widget->getColumnSpan());
    }

    public function test_list_quotations_table_has_actions(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($user);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        Livewire::test(ListQuotations::class)
            ->assertSuccessful()
            ->assertTableActionExists('convertToInvoice')
            ->assertTableActionExists('downloadPdf');
    }

    public function test_list_invoices_table_has_download_pdf_action(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($user);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        Livewire::test(ListInvoices::class)
            ->assertSuccessful()
            ->assertTableActionExists('downloadPdf');
    }
}
