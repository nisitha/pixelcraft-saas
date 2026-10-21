<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Quotations\Pages\ViewQuotation;
use App\Models\Category;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use App\Services\DocumentItemConsolidator;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class ConsolidatedPdfModeTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $owner;

    protected Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->company = Company::where('slug', 'lanka-solar-solutions')->first();
        $this->owner = User::where('email', 'owner@lankasolar.lk')->first();
        $this->contact = Contact::where('company_id', $this->company->id)->first();
    }

    public function test_quotation_and_invoice_have_default_consolidated_summary_mode(): void
    {
        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-DEFAULT-TEST',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => 500000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 500000.00,
            'status' => 'draft',
            'price_display_mode' => 'package_total_only',
        ]);

        $this->assertSame('consolidated', $quotation->pdf_item_summary_mode);

        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-DEFAULT-TEST',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'subtotal' => 500000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 500000.00,
            'paid_amount' => 0.00,
            'due_amount' => 500000.00,
            'status' => 'draft',
            'price_display_mode' => 'package_total_only',
        ]);

        $this->assertSame('consolidated', $invoice->pdf_item_summary_mode);
    }

    public function test_consolidator_engine_groups_twenty_plus_hardware_accessories_into_five_rows(): void
    {
        // 1. Major categories
        $panelCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Solar PV Panels'],
            ['slug' => 'solar-pv-panels', 'type' => 'product']
        );
        $inverterCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Inverters'],
            ['slug' => 'inverters', 'type' => 'product']
        );
        $batteryCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Battery Energy Storage'],
            ['slug' => 'batteries', 'type' => 'product']
        );
        $hardwareCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Mounting & Accessories'],
            ['slug' => 'mounting-accessories', 'type' => 'product']
        );
        $serviceCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Services & Installation'],
            ['slug' => 'services-installation', 'type' => 'service']
        );

        $panelProd = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $panelCategory->id,
            'name' => 'AE 630W Bifacial Solar PV Panels',
            'sku' => 'TEST-PANEL-630W',
            'selling_price' => 38000.00,
            'warranty_months' => 360,
        ]);

        $inverterProd = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $inverterCategory->id,
            'name' => '5kW Pure Sine Wave Hybrid Solar Inverter',
            'sku' => 'TEST-INV-5KW',
            'selling_price' => 195000.00,
            'warranty_months' => 120,
        ]);

        $batteryProd = Product::create([
            'company_id' => $this->company->id,
            'category_id' => $batteryCategory->id,
            'name' => 'LiFePO4 Lithium Battery 51.2V 100Ah Pack',
            'sku' => 'TEST-BAT-100AH',
            'selling_price' => 385000.00,
            'warranty_months' => 120,
        ]);

        // Items list: 1 Panel, 1 Inverter, 1 Battery, 21 Accessories, 2 Services = 26 total items!
        $testItems = collect();

        // 1. Panels
        $testItems->push(new QuotationItem([
            'product_id' => $panelProd->id,
            'description' => 'AE 630W Bifacial Solar PV Panels (8x Modules)',
            'quantity' => 8,
            'unit_price' => 38000.00,
            'subtotal' => 304000.00,
            'warranty_text' => '30 Years Linear Performance Warranty',
        ]));
        $testItems->last()->setRelation('product', $panelProd);

        // 2. Inverter
        $testItems->push(new QuotationItem([
            'product_id' => $inverterProd->id,
            'description' => '5kW Hybrid Solar Inverter 48V',
            'quantity' => 1,
            'unit_price' => 195000.00,
            'subtotal' => 195000.00,
            'warranty_text' => '10 Years Manufacturer Warranty',
        ]));
        $testItems->last()->setRelation('product', $inverterProd);

        // 3. Battery
        $testItems->push(new QuotationItem([
            'product_id' => $batteryProd->id,
            'description' => 'LiFePO4 Lithium Battery 5.12 kWh Storage',
            'quantity' => 1,
            'unit_price' => 385000.00,
            'subtotal' => 385000.00,
            'warranty_text' => '10 Years Battery Life Warranty',
        ]));
        $testItems->last()->setRelation('product', $batteryProd);

        // 4. 21 Hardware Accessories
        $accessoriesList = [
            ['D Rail 04 Solar Aluminum Mounting Rails', 4, 8500.00],
            ['Mid Clamps 35mm Aluminum', 16, 250.00],
            ['End Clamps 35mm Aluminum', 8, 250.00],
            ['L-Feet Heavy Duty Brackets', 12, 450.00],
            ['Stainless Steel Tile Roof Hooks', 12, 1200.00],
            ['DC Circuit Breaker 2P 40A 550V', 2, 6800.00],
            ['DC SPD BUD-40/2R Surge Arrester 600V', 2, 7500.00],
            ['AC SPD Type 2 Surge Protector 230V', 1, 4500.00],
            ['AC Circuit Breaker 32A Double Pole', 2, 3200.00],
            ['Changeover Switch 40A Manual', 1, 6500.00],
            ['4mm Lapp DC Solar Cable (Red)', 35, 380.00],
            ['4mm Lapp DC Solar Cable (Black)', 35, 380.00],
            ['6mm Earth Grounding Cable (Green/Yellow)', 25, 290.00],
            ['Copper Bonded Earth Rod 5Ft with Clamp', 1, 5200.00],
            ['Earth Inspection Pit Chamber & Cover', 1, 4500.00],
            ['IP65 Weatherproof DC Combiner Enclosure', 1, 14500.00],
            ['IP65 AC Distribution Box (8-Way)', 1, 8500.00],
            ['MC4 Solar Connectors Male/Female Pairs', 6, 450.00],
            ['Corrugated Heavy Duty PVC Conduits (25mm)', 10, 450.00],
            ['Cable Trunking 50x50mm Casing', 6, 1200.00],
            ['Solar Hazard & Safety Label Warning Kit', 1, 2500.00],
        ];

        $expectedAccSubtotal = 0.0;
        foreach ($accessoriesList as $index => [$name, $qty, $rate]) {
            $prod = Product::create([
                'company_id' => $this->company->id,
                'category_id' => $hardwareCategory->id,
                'name' => $name,
                'sku' => "ACC-TEST-{$index}",
                'selling_price' => $rate,
            ]);
            $subtotal = $qty * $rate;
            $expectedAccSubtotal += $subtotal;

            $item = new QuotationItem([
                'product_id' => $prod->id,
                'description' => $name,
                'quantity' => $qty,
                'unit_price' => $rate,
                'subtotal' => $subtotal,
            ]);
            $item->setRelation('product', $prod);
            $testItems->push($item);
        }

        // 5. 2 Services
        $servicesList = [
            ['Site Logistics, Material Delivery & Transport', 1, 25000.00],
            ['Mechanical Mounting & Turnkey Electrical Installation', 1, 85000.00],
        ];

        $expectedSrvSubtotal = 0.0;
        foreach ($servicesList as $index => [$name, $qty, $rate]) {
            $prod = Product::create([
                'company_id' => $this->company->id,
                'category_id' => $serviceCategory->id,
                'name' => $name,
                'sku' => "SRV-TEST-{$index}",
                'selling_price' => $rate,
            ]);
            $subtotal = $qty * $rate;
            $expectedSrvSubtotal += $subtotal;

            $item = new QuotationItem([
                'product_id' => $prod->id,
                'description' => $name,
                'quantity' => $qty,
                'unit_price' => $rate,
                'subtotal' => $subtotal,
            ]);
            $item->setRelation('product', $prod);
            $testItems->push($item);
        }

        // Test Raw Count
        $this->assertCount(26, $testItems);

        // Test Consolidated Mode Output
        $consolidated = DocumentItemConsolidator::prepareItemsForDisplay($testItems, consolidate: true);
        $this->assertCount(5, $consolidated, 'Consolidated summary must produce exactly 5 rows');

        // Check Row 1 (Panels)
        $this->assertSame('AE 630W Bifacial Solar PV Panels', $consolidated[0]->product->name);
        $this->assertEquals(8, $consolidated[0]->quantity);

        // Check Row 2 (Inverter)
        $this->assertSame('5kW Pure Sine Wave Hybrid Solar Inverter', $consolidated[1]->product->name);
        $this->assertEquals(1, $consolidated[1]->quantity);

        // Check Row 3 (Battery)
        $this->assertSame('LiFePO4 Lithium Battery 51.2V 100Ah Pack', $consolidated[2]->product->name);
        $this->assertEquals(1, $consolidated[2]->quantity);

        // Check Row 4 (Consolidated Accessories)
        $row4 = $consolidated[3];
        $this->assertSame('Solar Mounting Structures, Surge Protection (SPD), DC/AC Switchgear & Electrical Accessories', $row4->product->name);
        $this->assertStringContainsString('Balance of System (BOS) Accessories & Hardware', $row4->description);
        $this->assertSame('1 Set', $row4->formatted_quantity);
        $this->assertEquals($expectedAccSubtotal, (float) $row4->subtotal);

        // Check Row 5 (Consolidated Services)
        $row5 = $consolidated[4];
        $this->assertSame('Site Transport, Mechanical Mounting & Turnkey Electrical Installation', $row5->product->name);
        $this->assertStringContainsString('Site Logistics, Transport & Turnkey Installation Services', $row5->description);
        $this->assertSame('1 Job', $row5->formatted_quantity);
        $this->assertEquals($expectedSrvSubtotal, (float) $row5->subtotal);

        // Test Detailed Mode Output
        $detailed = DocumentItemConsolidator::prepareItemsForDisplay($testItems, consolidate: false);
        $this->assertCount(26, $detailed, 'Detailed mode must preserve all 26 individual items');
    }

    public function test_consolidated_pdf_fits_strictly_on_single_a4_page_with_twenty_plus_accessories(): void
    {
        // Setup Fernando's Quotation with 20+ items
        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'VHE/2026/RU/00199/TEST-20',
            'quote_date' => '2026-10-10',
            'valid_until' => '2026-10-25',
            'status' => 'accepted',
            'system_kw' => 5.0,
            'phase_type' => 'Single',
            'price_display_mode' => 'package_total_only',
            'pdf_item_summary_mode' => 'consolidated',
            'subtotal' => 1250000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 1250000.00,
            'terms' => "1. Valid for 14 days.\n2. Milestone 1: 50% advance.\n3. Milestone 2: 50% commissioning.\n4. Comprehensive 2-year warranty.",
        ]);

        $panelCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Solar PV Panels'],
            ['slug' => 'cat-solar-pv-panels', 'type' => 'product']
        );
        $inverterCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Inverters'],
            ['slug' => 'cat-inverters', 'type' => 'product']
        );
        $batteryCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Battery Energy Storage'],
            ['slug' => 'cat-batteries', 'type' => 'product']
        );
        $hardwareCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Mounting & Accessories'],
            ['slug' => 'cat-mounting-accessories', 'type' => 'product']
        );
        $serviceCategory = Category::firstOrCreate(
            ['company_id' => $this->company->id, 'name' => 'Services & Installation'],
            ['slug' => 'cat-services-installation', 'type' => 'service']
        );

        $p1 = Product::create(['company_id' => $this->company->id, 'category_id' => $panelCategory->id, 'name' => 'AE 630W Bifacial Solar PV Panels', 'selling_price' => 38000.00]);
        $p2 = Product::create(['company_id' => $this->company->id, 'category_id' => $inverterCategory->id, 'name' => '5kW Pure Sine Wave Hybrid Inverter', 'selling_price' => 195000.00]);
        $p3 = Product::create(['company_id' => $this->company->id, 'category_id' => $batteryCategory->id, 'name' => 'LiFePO4 Lithium Battery 51.2V 100Ah Pack', 'selling_price' => 385000.00]);

        $quotation->items()->create(['product_id' => $p1->id, 'description' => $p1->name, 'quantity' => 8, 'unit_price' => 38000.00, 'subtotal' => 304000.00, 'warranty_text' => '30 Years']);
        $quotation->items()->create(['product_id' => $p2->id, 'description' => $p2->name, 'quantity' => 1, 'unit_price' => 195000.00, 'subtotal' => 195000.00, 'warranty_text' => '10 Years']);
        $quotation->items()->create(['product_id' => $p3->id, 'description' => $p3->name, 'quantity' => 1, 'unit_price' => 385000.00, 'subtotal' => 385000.00, 'warranty_text' => '10 Years']);

        // Add 20 accessory items
        for ($i = 1; $i <= 20; $i++) {
            $accProd = Product::create([
                'company_id' => $this->company->id,
                'category_id' => $hardwareCategory->id,
                'name' => "Hardware Accessory Item #{$i}",
                'selling_price' => 5000.00,
            ]);

            $quotation->items()->create([
                'product_id' => $accProd->id,
                'description' => $accProd->name,
                'quantity' => 2,
                'unit_price' => 5000.00,
                'subtotal' => 10000.00,
            ]);
        }

        // Add 2 service items
        $s1 = Product::create(['company_id' => $this->company->id, 'category_id' => $serviceCategory->id, 'name' => 'Site Logistics & Transport Delivery', 'selling_price' => 30000.00]);
        $s2 = Product::create(['company_id' => $this->company->id, 'category_id' => $serviceCategory->id, 'name' => 'Turnkey Engineering Mounting & Testing', 'selling_price' => 136000.00]);
        $quotation->items()->create(['product_id' => $s1->id, 'description' => $s1->name, 'quantity' => 1, 'unit_price' => 30000.00, 'subtotal' => 30000.00]);
        $quotation->items()->create(['product_id' => $s2->id, 'description' => $s2->name, 'quantity' => 1, 'unit_price' => 136000.00, 'subtotal' => 136000.00]);

        // Total raw items = 3 + 20 + 2 = 25 items!
        $this->assertEquals(25, $quotation->items()->count());

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateQuotationPdf($quotation, 'package_total_only', 'consolidated');

        // Render PDF and verify exact canvas page count is 1
        $pdf->render();
        $canvas = $pdf->getDomPDF()->getCanvas();
        $pageCount = $canvas->get_page_count();

        $this->assertEquals(1, $pageCount, "The Customer Package Consolidated Quotation must fit strictly on 1 Single A4 page (actual pages: {$pageCount})");

        $output = $pdf->output();
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);
    }

    public function test_consolidated_invoice_pdf_fits_strictly_on_single_a4_page(): void
    {
        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-2026-TEST-SINGLE-PAGE',
            'invoice_date' => '2026-10-12',
            'due_date' => '2026-10-18',
            'type' => 'milestone',
            'price_display_mode' => 'package_total_only',
            'pdf_item_summary_mode' => 'consolidated',
            'subtotal' => 1250000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 1250000.00,
            'paid_amount' => 1250000.00,
            'due_amount' => 0.00,
            'status' => 'paid',
            'terms' => 'Standard milestone payment invoice terms.',
        ]);

        $panelCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Solar PV Panels'], ['slug' => 'pv-pan', 'type' => 'product']);
        $invCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Inverters'], ['slug' => 'pv-inv', 'type' => 'product']);
        $batCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Batteries'], ['slug' => 'pv-bat', 'type' => 'product']);
        $accCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Hardware Accessories'], ['slug' => 'pv-acc', 'type' => 'product']);
        $srvCat = Category::firstOrCreate(['company_id' => $this->company->id, 'name' => 'Installation Services'], ['slug' => 'pv-srv', 'type' => 'service']);

        $p1 = Product::create(['company_id' => $this->company->id, 'category_id' => $panelCat->id, 'name' => 'AE Solar 630W Bifacial Panels', 'selling_price' => 38000.00]);
        $p2 = Product::create(['company_id' => $this->company->id, 'category_id' => $invCat->id, 'name' => 'Off-Grid Inverter 5kW', 'selling_price' => 195000.00]);
        $p3 = Product::create(['company_id' => $this->company->id, 'category_id' => $batCat->id, 'name' => 'LiFePO4 Battery 51.2V 100Ah', 'selling_price' => 385000.00]);

        $invoice->items()->create(['product_id' => $p1->id, 'description' => $p1->name, 'quantity' => 8, 'unit_price' => 38000.00, 'subtotal' => 304000.00]);
        $invoice->items()->create(['product_id' => $p2->id, 'description' => $p2->name, 'quantity' => 1, 'unit_price' => 195000.00, 'subtotal' => 195000.00]);
        $invoice->items()->create(['product_id' => $p3->id, 'description' => $p3->name, 'quantity' => 1, 'unit_price' => 385000.00, 'subtotal' => 385000.00]);

        for ($i = 1; $i <= 18; $i++) {
            $acc = Product::create(['company_id' => $this->company->id, 'category_id' => $accCat->id, 'name' => "Solar Hardware Line Item {$i}", 'selling_price' => 5000.00]);
            $invoice->items()->create(['product_id' => $acc->id, 'description' => $acc->name, 'quantity' => 2, 'unit_price' => 5000.00, 'subtotal' => 10000.00]);
        }

        $srv = Product::create(['company_id' => $this->company->id, 'category_id' => $srvCat->id, 'name' => 'Turnkey Installation Services', 'selling_price' => 186000.00]);
        $invoice->items()->create(['product_id' => $srv->id, 'description' => $srv->name, 'quantity' => 1, 'unit_price' => 186000.00, 'subtotal' => 186000.00]);

        $this->assertEquals(22, $invoice->items()->count());

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateInvoicePdf($invoice, 'package_total_only', 'consolidated');

        $pdf->render();
        $pageCount = $pdf->getDomPDF()->getCanvas()->get_page_count();

        $this->assertEquals(1, $pageCount, "The Customer Package Consolidated Invoice must fit strictly on 1 Single A4 page (actual pages: {$pageCount})");
    }

    public function test_split_view_livewire_component_allows_toggling_pdf_item_summary_mode(): void
    {
        $this->actingAs($this->owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);

        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-SPLIT-SUMMARY-TOGGLE',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'subtotal' => 200000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 200000.00,
            'status' => 'draft',
            'price_display_mode' => 'itemized',
            'pdf_item_summary_mode' => 'consolidated',
        ]);

        Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
            ->assertSee('Consolidated (Strict 1-Page)')
            ->assertSet('previewSummaryMode', 'consolidated')
            ->call('togglePreviewPdfItemSummaryMode')
            ->assertSet('previewSummaryMode', 'detailed')
            ->assertSee('Detailed Breakdown');
    }

    public function test_public_document_preview_and_download_support_summary_mode(): void
    {
        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-PUBLIC-MODE-TEST',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'subtotal' => 300000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 300000.00,
            'status' => 'sent',
            'price_display_mode' => 'package_total_only',
            'pdf_item_summary_mode' => 'consolidated',
        ]);

        $previewUrl = URL::signedRoute('public.document.show', [
            'type' => 'quotation',
            'id' => $quotation->id,
        ]);
        $response = $this->get($previewUrl);
        $response->assertStatus(200);
        $response->assertSee('Download Official PDF');
        $response->assertSee('summary_mode=consolidated');

        $downloadUrl = URL::signedRoute('public.document.download', [
            'type' => 'quotation',
            'id' => $quotation->id,
            'mode' => 'package_total_only',
            'summary_mode' => 'consolidated',
        ]);

        $downloadResponse = $this->get($downloadUrl);
        $downloadResponse->assertStatus(200);
        $this->assertSame('application/pdf', $downloadResponse->headers->get('Content-Type'));
    }
}
