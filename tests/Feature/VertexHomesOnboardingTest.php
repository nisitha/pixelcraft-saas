<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VertexHomesOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_vertex_homes_tenant_company_is_seeded_properly(): void
    {
        $company = Company::where('slug', 'vertex-homes')->first();

        $this->assertNotNull($company);
        $this->assertEquals('VERTEX HOME SOLUTIONS (PVT) LTD', $company->name);
        $this->assertEquals('vertex-homes', $company->slug);
        $this->assertEquals('+94 70 2 271 271 / +94 33 224 1241', $company->phone);
        $this->assertEquals('operations@vertexhomeslk.com', $company->email);
        $this->assertEquals('No. 33/A, Hapugahagedara, Loluwagoda, Mirigama, Sri Lanka', $company->address);
        $this->assertTrue($company->is_active);

        // Verify Settings JSON
        $this->assertIsArray($company->settings);
        $this->assertEquals('vertexhomeslk.com', $company->settings['website']);
        $this->assertStringContainsString('DFCC Bank', $company->settings['bank_details']);
        $this->assertStringContainsString('101001490231', $company->settings['bank_details']);
        $this->assertStringContainsString('Bank Of Ceylon (BOC)', $company->settings['bank_details']);
        $this->assertStringContainsString('97194769', $company->settings['bank_details']);

        $this->assertStringContainsString('Valid for 07 days from the date of issue.', $company->settings['default_quote_terms']);
        $this->assertStringContainsString('60% advance upon confirmation, 20% on delivery, 20% after commissioning.', $company->settings['default_quote_terms']);
        $this->assertStringContainsString('Karunanayaka - 0777 48 15 34', $company->settings['default_quote_terms']);
    }

    public function test_vertex_homes_owner_user_is_created_and_attached(): void
    {
        $company = Company::where('slug', 'vertex-homes')->first();
        $owner = User::where('email', 'operations@vertexhomeslk.com')->first();

        $this->assertNotNull($owner);
        $this->assertTrue($owner->is_active);
        $this->assertEquals('owner', $owner->roleInTenant($company));
        $this->assertTrue($owner->isTenantOwnerOrAdmin($company));
    }

    public function test_all_eight_categories_are_created_under_vertex_homes(): void
    {
        $company = Company::where('slug', 'vertex-homes')->first();

        $expectedCategories = [
            'Inverters',
            'Battery Energy Storage',
            'Solar PV Panels',
            'Mounting & Hardware',
            'Electrical Switchgear & Protection',
            'Cables & Wiring',
            'Installation & Engineering Services',
            'Logistics & Transport',
        ];

        foreach ($expectedCategories as $categoryName) {
            $this->assertTrue(
                Category::where('company_id', $company->id)->where('name', $categoryName)->exists(),
                "Category [{$categoryName}] was not found under company [{$company->name}]."
            );
        }
    }

    public function test_products_accessories_and_services_are_populated_with_exact_prices(): void
    {
        $company = Company::where('slug', 'vertex-homes')->first();

        // 1. Inverter GW10K-SDT-30 (120 months)
        $inverter = Product::where('company_id', $company->id)->where('sku', 'INV-GW10K-SDT-30')->first();
        $this->assertNotNull($inverter);
        $this->assertEquals(120, $inverter->warranty_months);
        $this->assertTrue($inverter->track_serial);

        // 2. Lithium Battery Pack 16kWh-(IP21) (680,000.00, 120 months)
        $battery = Product::where('company_id', $company->id)->where('sku', 'BAT-LITH-16KWH-IP21')->first();
        $this->assertNotNull($battery);
        $this->assertEquals(680000.00, (float) $battery->unit_price);
        $this->assertEquals(120, $battery->warranty_months);
        $this->assertTrue($battery->track_serial);

        // 3. Solar Panels METEOR 610W-630W (35,800.00, 360 months)
        $panel = Product::where('company_id', $company->id)->where('sku', 'PANEL-METEOR-610-630W')->first();
        $this->assertNotNull($panel);
        $this->assertEquals(35800.00, (float) $panel->unit_price);
        $this->assertEquals(360, $panel->warranty_months);

        // 4. Mounting & Hardware items
        $this->assertEquals(7000.00, (float) Product::where('company_id', $company->id)->where('sku', 'MNT-RELLING-BAR')->value('unit_price'));
        $this->assertEquals(1392.00, (float) Product::where('company_id', $company->id)->where('sku', 'MNT-TILLE-WOOD')->value('unit_price'));
        $this->assertEquals(450.00, (float) Product::where('company_id', $company->id)->where('sku', 'MNT-T-CLAMP')->value('unit_price'));
        $this->assertEquals(380.00, (float) Product::where('company_id', $company->id)->where('sku', 'MNT-END-CLAMP')->value('unit_price'));

        // 5. Electrical Switchgear items
        $this->assertEquals(5500.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-BENY-DC-SPD-2P')->value('unit_price'));
        $this->assertEquals(13500.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-ATS-AUTO-SWITCH')->value('unit_price'));
        $this->assertEquals(3800.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-BENY-DC-MCB-2P')->value('unit_price'));
        $this->assertEquals(3500.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-BENY-AC-SPD-2P')->value('unit_price'));
        $this->assertEquals(1300.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-BENY-AC-MCB-2P')->value('unit_price'));

        // 6. DB Boxes
        $this->assertEquals(3250.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-DB-BOX-9WAY')->value('unit_price'));
        $this->assertEquals(2850.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-DB-BOX-6WAY')->value('unit_price'));
        $this->assertEquals(2500.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-DB-BOX-4WAY')->value('unit_price'));
        $this->assertEquals(2900.00, (float) Product::where('company_id', $company->id)->where('sku', 'SW-DB-BOX-8WAY')->value('unit_price'));

        // 7. Cables & Wiring
        $this->assertEquals(250.00, (float) Product::where('company_id', $company->id)->where('sku', 'CAB-DC-WIRE-BLACK')->value('unit_price'));
        $this->assertEquals(250.00, (float) Product::where('company_id', $company->id)->where('sku', 'CAB-DC-WIRE-RED')->value('unit_price'));
        $this->assertEquals(520.00, (float) Product::where('company_id', $company->id)->where('sku', 'CAB-AC-WIRE-1M')->value('unit_price'));
        $this->assertEquals(290.00, (float) Product::where('company_id', $company->id)->where('sku', 'CAB-EARTH-WIRE-4MM')->value('unit_price'));
        $this->assertEquals(2670.00, (float) Product::where('company_id', $company->id)->where('sku', 'CAB-25-SGG-CABLE')->value('unit_price'));

        // 8. Services & Installation
        $this->assertEquals(750.00, (float) Product::where('company_id', $company->id)->where('sku', 'SRV-PANEL-SERVICE-UNIT')->value('unit_price'));
        $this->assertEquals(150000.00, (float) Product::where('company_id', $company->id)->where('sku', 'SRV-PANEL-INSTALL')->value('unit_price'));
        $this->assertEquals(10000.00, (float) Product::where('company_id', $company->id)->where('sku', 'SRV-BATTERY-INSTALL')->value('unit_price'));

        // 9. Transport
        $this->assertEquals(7500.00, (float) Product::where('company_id', $company->id)->where('sku', 'TRN-KADAWATHA-MAIN-STORE')->value('unit_price'));
        $this->assertEquals(3000.00, (float) Product::where('company_id', $company->id)->where('sku', 'TRN-MAIN-STORE-MIRIGAMA')->value('unit_price'));

        // Verify Total Products count under company is over 35 items
        $this->assertGreaterThan(35, Product::where('company_id', $company->id)->count());
    }

    public function test_csv_quotation_and_customer_are_seeded(): void
    {
        $company = Company::where('slug', 'vertex-homes')->first();
        $customer = Contact::where('company_id', $company->id)->where('name', 'Mr. Zuffikkar')->first();

        $this->assertNotNull($customer);
        $this->assertStringContainsString('Kollupitiya', $customer->address);

        $quotation = Quotation::where('company_id', $company->id)->where('quote_number', 'VHE/2026/MAD/00205/011-B')->first();
        $this->assertNotNull($quotation);
        $this->assertEquals(4060375.00, (float) $quotation->grand_total);
        $this->assertEquals(5, $quotation->items()->count());
    }
}
