<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_super_admin_user_and_plans_seeded(): void
    {
        $superAdmin = User::where('email', 'admin@vertexinnovationlk.com')->first();
        $this->assertNotNull($superAdmin);
        $this->assertTrue((bool) $superAdmin->is_super_admin);
        $this->assertTrue((bool) $superAdmin->is_active);

        $this->assertEquals(3, Plan::count());
        $this->assertNotNull(Plan::where('slug', 'bronze')->first());
        $this->assertNotNull(Plan::where('slug', 'silver')->first());
        $this->assertNotNull(Plan::where('slug', 'gold')->first());

        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $this->assertNotNull($company);
        $this->assertEquals('Gold', $company->plan->name);
        $this->assertTrue($company->isSubscriptionActive());
    }

    public function test_super_admin_panel_authorization(): void
    {
        $superAdmin = User::where('email', 'admin@vertexinnovationlk.com')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();
        $superPanel = Filament::getPanel('admin');

        $this->assertTrue($superAdmin->canAccessPanel($superPanel));
        $this->assertFalse($owner->canAccessPanel($superPanel));
    }

    public function test_tenant_owner_app_panel_and_tenant_access(): void
    {
        $owner = User::where('email', 'owner@lankasolar.lk')->first();
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $appPanel = Filament::getPanel('app');

        $this->assertTrue($owner->canAccessPanel($appPanel));
        $this->assertTrue($owner->canAccessTenant($company));

        $tenants = $owner->getTenants($appPanel);
        $this->assertTrue($tenants->contains('id', $company->id));
    }

    public function test_tenant_relationships_and_solar_data(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();

        $this->assertGreaterThan(0, $company->categories()->count());
        $this->assertGreaterThan(0, $company->products()->count());
        $this->assertGreaterThan(0, $company->contacts()->count());
        $this->assertGreaterThan(0, $company->quotations()->count());
        $this->assertGreaterThan(0, $company->invoices()->count());
        $this->assertGreaterThan(0, $company->payments()->count());
        $this->assertGreaterThan(0, $company->expenses()->count());
        $this->assertGreaterThan(0, $company->auditLogs()->count());

        $quotation = Quotation::where('quote_number', 'QUO-2026-0001')->first();
        $this->assertNotNull($quotation);
        $this->assertEquals(10.53, (float) $quotation->system_kw);
        $this->assertEquals('three', $quotation->phase_type);
        $this->assertEquals(4, $quotation->items()->count());

        $invoice = Invoice::where('invoice_number', 'INV-2026-0001')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('milestone', $invoice->type);
        $this->assertEquals(1300000.00, (float) $invoice->grand_total);
        $this->assertEquals(650000.00, (float) $invoice->paid_amount);
        $this->assertEquals(650000.00, (float) $invoice->due_amount);
        $this->assertEquals('partial', $invoice->status);
        $this->assertEquals(1, $invoice->payments()->count());

        $payment = $invoice->payments()->first();
        $this->assertEquals('bank_transfer', $payment->method);
        $this->assertEquals(650000.00, (float) $payment->amount);
    }

    public function test_inactive_or_expired_subscription_check(): void
    {
        $activeCompany = new Company([
            'is_active' => true,
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $this->assertTrue($activeCompany->isSubscriptionActive());

        $inactiveCompany = new Company([
            'is_active' => false,
            'subscription_ends_at' => now()->addMonth(),
        ]);
        $this->assertFalse($inactiveCompany->isSubscriptionActive());

        $expiredCompany = new Company([
            'is_active' => true,
            'subscription_ends_at' => now()->subDay(),
        ]);
        $this->assertFalse($expiredCompany->isSubscriptionActive());
    }
}
