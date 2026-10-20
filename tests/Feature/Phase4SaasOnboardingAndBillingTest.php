<?php

namespace Tests\Feature;

use App\Filament\App\Pages\SubscriptionBilling;
use App\Filament\App\Pages\Tenancy\EditCompanyProfile;
use App\Filament\App\Pages\Tenancy\RegisterCompany;
use App\Filament\SuperAdmin\Resources\SubscriptionRequests\Pages\ListSubscriptionRequests;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Quotation;
use App\Models\SubscriptionRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class Phase4SaasOnboardingAndBillingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_landing_page_renders_with_active_plans(): void
    {
        // Deactivate or add an inactive plan to verify exclusion
        Plan::create([
            'name' => 'Secret Enterprise',
            'slug' => 'secret-enterprise',
            'price_monthly' => 99999.00,
            'price_yearly' => 999999.00,
            'max_users' => 100,
            'max_invoices' => 100000,
            'features' => ['private_cloud'],
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Vertex Books');
        $response->assertSee('Smart Cloud Accounting');
        $response->assertSee('Solar ERP');
        $response->assertSee('Bronze');
        $response->assertSee('Silver');
        $response->assertSee('Gold');
        $response->assertSee('Rs. 2,500');
        $response->assertSee('Rs. 4,500');
        $response->assertSee('Rs. 7,500');
        $response->assertSee('Start Free Trial');
        $response->assertSee('vertexinnovationlk.com');
        $response->assertDontSee('Secret Enterprise');
    }

    public function test_tenant_registration_assigns_14_day_trial_and_owner_role(): void
    {
        $user = User::create([
            'name' => 'Nuwan Pradeep',
            'email' => 'nuwan@ceylonsolar.lk',
            'password' => Hash::make('Password@123'),
            'is_super_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $silverPlan = Plan::where('slug', 'silver')->first();

        Livewire::test(RegisterCompany::class)
            ->fillForm([
                'name' => 'Ceylon Solar Technologies',
                'phone' => '+94 71 234 5678',
                'email' => 'contact@ceylonsolar.lk',
                'address' => 'No. 88, Galle Road, Colombo 04',
                'tax_number' => 'VAT-77665544',
                'plan_id' => $silverPlan->id,
            ])
            ->call('register')
            ->assertHasNoFormErrors();

        $company = Company::where('slug', 'ceylon-solar-technologies')->first();
        $this->assertNotNull($company);
        $this->assertEquals('Ceylon Solar Technologies', $company->name);
        $this->assertEquals('+94 71 234 5678', $company->phone);
        $this->assertEquals('contact@ceylonsolar.lk', $company->email);
        $this->assertEquals('VAT-77665544', $company->tax_number);
        $this->assertEquals($silverPlan->id, $company->plan_id);
        $this->assertTrue($company->is_active);

        // Verify 14-day trial
        $this->assertNotNull($company->subscription_ends_at);
        $this->assertEquals(14, round(now()->diffInDays($company->subscription_ends_at)));

        // Verify user attached as owner
        $this->assertTrue($user->companies()->where('companies.id', $company->id)->exists());
        $this->assertEquals('owner', $user->roleInTenant($company));

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $user->id,
            'action' => 'registered_tenant',
            'model_type' => Company::class,
            'model_id' => $company->id,
        ]);
    }

    public function test_tenant_profile_settings_update_company_details_and_settings_json(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        Livewire::test(EditCompanyProfile::class)
            ->fillForm([
                'name' => 'Lanka Solar Solutions (Pvt) Ltd',
                'phone' => '+94 11 777 9999',
                'email' => 'billing@lankasolar.lk',
                'tax_number' => 'VAT-9988112233',
                'address' => 'Updated Landmark Tower, Colombo 03',
                'settings.bank_details' => "Commercial Bank PLC\nAccount: 8849201948\nBranch: Kollupitiya",
                'settings.default_quote_terms' => 'Standard quotation valid for 45 calendar days.',
                'settings.default_invoice_terms' => 'Milestone payments due within 7 days of stage certificate.',
                'settings.pos_footer_note' => 'Thank you for choosing Lanka Solar!',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $company->refresh();
        $this->assertEquals('Lanka Solar Solutions (Pvt) Ltd', $company->name);
        $this->assertEquals('+94 11 777 9999', $company->phone);
        $this->assertEquals('billing@lankasolar.lk', $company->email);
        $this->assertEquals('VAT-9988112233', $company->tax_number);
        $this->assertEquals('Updated Landmark Tower, Colombo 03', $company->address);
        $this->assertEquals("Commercial Bank PLC\nAccount: 8849201948\nBranch: Kollupitiya", $company->settings['bank_details'] ?? null);
        $this->assertEquals('Standard quotation valid for 45 calendar days.', $company->settings['default_quote_terms'] ?? null);
        $this->assertEquals('Milestone payments due within 7 days of stage certificate.', $company->settings['default_invoice_terms'] ?? null);
        $this->assertEquals('Thank you for choosing Lanka Solar!', $company->settings['pos_footer_note'] ?? null);
    }

    public function test_pdf_templates_render_with_company_settings_bank_details_and_terms(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $company->update([
            'settings' => array_merge($company->settings ?? [], [
                'bank_details' => "Sampath Bank PLC\nAccount: 10023456789\nBranch: Kandy City Centre",
                'default_quote_terms' => 'Custom Quote Term: 60 Days Warranty on Labour',
                'default_invoice_terms' => 'Custom Invoice Term: Late payments incur 2% interest',
            ]),
        ]);

        $quotation = Quotation::where('company_id', $company->id)->first();
        $quotation->update(['terms' => null]); // Force fallback to company setting

        $invoice = Invoice::where('company_id', $company->id)->first();
        $contact = Contact::where('company_id', $company->id)->first();

        // Render quotation PDF view
        $quoteView = view('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $company,
            'contact' => $contact,
            'items' => $quotation->items,
        ])->render();

        $this->assertStringContainsString('Custom Quote Term: 60 Days Warranty on Labour', $quoteView);

        // Render invoice PDF view
        $invoiceView = view('pdf.invoice', [
            'invoice' => $invoice,
            'quotation' => $quotation,
            'company' => $company,
            'contact' => $contact,
            'items' => $invoice->items,
            'payments' => $invoice->payments,
        ])->render();

        $this->assertStringContainsString('Sampath Bank PLC', $invoiceView);
        $this->assertStringContainsString('10023456789', $invoiceView);
        $this->assertStringContainsString('Custom Invoice Term: Late payments incur 2% interest', $invoiceView);
    }

    public function test_subscription_renewal_request_workflow_allows_access_when_expired(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();

        // Expire subscription and deactivate company
        $company->update([
            'is_active' => false,
            'subscription_ends_at' => now()->subDays(5),
        ]);

        $this->assertFalse($company->isSubscriptionActive());

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        // 1. Ensure GET request to /app/{tenant:slug}/subscription-billing does not return 403
        $response = $this->get("/app/{$company->slug}/subscription-billing");
        $response->assertStatus(200);

        // 2. Submit renewal request on SubscriptionBilling page
        $goldPlan = Plan::where('slug', 'gold')->first();

        Livewire::test(SubscriptionBilling::class)
            ->set('plan_id', $goldPlan->id)
            ->set('billing_cycle', 'yearly')
            ->set('payment_reference', 'BOC-TXN-RENEW-2026')
            ->set('notes', 'Annual renewal transfer from Lanka Solar BOC corporate account')
            ->call('submitRenewalRequest')
            ->assertHasNoErrors();

        // Verify request stored in database
        $this->assertDatabaseHas('subscription_requests', [
            'company_id' => $company->id,
            'plan_id' => $goldPlan->id,
            'billing_cycle' => 'yearly',
            'amount' => $goldPlan->price_yearly,
            'payment_reference' => 'BOC-TXN-RENEW-2026',
            'status' => 'pending',
        ]);
    }

    public function test_super_admin_approves_and_extends_subscription_reactivating_company(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $goldPlan = Plan::where('slug', 'gold')->first();
        $superAdmin = User::where('email', 'admin@vertexinnovationlk.com')->first();

        // Expired company
        $company->update([
            'is_active' => false,
            'subscription_ends_at' => now()->subDays(10),
        ]);

        // Pending renewal request for 1 year
        $request = SubscriptionRequest::create([
            'company_id' => $company->id,
            'plan_id' => $goldPlan->id,
            'billing_cycle' => 'yearly',
            'amount' => $goldPlan->price_yearly,
            'payment_reference' => 'COM-BANK-REF-998877',
            'status' => 'pending',
        ]);

        $this->actingAs($superAdmin);
        Filament::setCurrentPanel(Filament::getPanel('super-admin'));

        // Super Admin approves and extends
        Livewire::test(ListSubscriptionRequests::class)
            ->callAction(TestAction::make('approve_extend')->table($request));

        $request->refresh();
        $this->assertEquals('approved', $request->status);
        $this->assertEquals($superAdmin->id, $request->approved_by);
        $this->assertNotNull($request->approved_at);

        $company->refresh();
        $this->assertTrue($company->is_active);
        $this->assertEquals($goldPlan->id, $company->plan_id);

        // Subscription should be extended by 1 year from now
        $this->assertTrue($company->isSubscriptionActive());
        $this->assertGreaterThan(now()->addMonths(11), $company->subscription_ends_at);

        // Verify audit log entry
        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $superAdmin->id,
            'action' => 'approved_subscription_renewal',
            'model_type' => SubscriptionRequest::class,
            'model_id' => $request->id,
        ]);
    }

    public function test_pixel_craft_studios_provider_details_and_human_readable_time_remaining(): void
    {
        $this->assertSame('Pixel Craft Studios', SubscriptionBilling::PROVIDER_NAME);
        $this->assertSame('Hatton National Bank PLC (HNB)', SubscriptionBilling::BANK_NAME);
        $this->assertSame('Galle Branch', SubscriptionBilling::BANK_BRANCH);
        $this->assertSame('P M N S T Dharmarathna', SubscriptionBilling::BANK_ACCOUNT_NAME);
        $this->assertSame('227020114025', SubscriptionBilling::BANK_ACCOUNT_NUMBER);
        $this->assertSame('Sri Lanka', SubscriptionBilling::BANK_COUNTRY);
        $this->assertSame('pixelcraftstudioslk@gmail.com', SubscriptionBilling::PAYPAL_EMAIL);

        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        // 1. Future expiry: ~365 days
        $company->update(['subscription_ends_at' => now()->addDays(365)]);
        $page = new SubscriptionBilling;
        $info = $page->getHumanReadableTimeRemaining();
        $this->assertFalse($info['is_expired']);
        $this->assertStringNotContainsString('.', $info['text']);
        $this->assertStringContainsString('365 Days remaining', $info['text']);

        // 2. Expired 5 days ago
        $company->update(['subscription_ends_at' => now()->subDays(5)]);
        $infoExpired = $page->getHumanReadableTimeRemaining();
        $this->assertTrue($infoExpired['is_expired']);
        $this->assertStringNotContainsString('.', $infoExpired['text']);
        $this->assertSame('Expired 5 days ago', $infoExpired['text']);
    }

    public function test_multi_method_subscription_renewal_paypal_and_direct_card_activation(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();
        $silverPlan = Plan::where('slug', 'silver')->first();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        // 1. Submit PayPal renewal request
        Livewire::test(SubscriptionBilling::class)
            ->set('plan_id', $silverPlan->id)
            ->set('billing_cycle', 'monthly')
            ->set('payment_method', 'paypal')
            ->set('payment_reference', 'PP-TXN-99887766')
            ->call('submitRenewalRequest')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('subscription_requests', [
            'company_id' => $company->id,
            'plan_id' => $silverPlan->id,
            'billing_cycle' => 'monthly',
            'payment_method' => 'paypal',
            'payment_reference' => 'PP-TXN-99887766',
            'status' => 'pending',
        ]);

        // 2. Direct Instant Card Payment
        $goldPlan = Plan::where('slug', 'gold')->first();
        $company->update(['subscription_ends_at' => now()->subDays(2), 'is_active' => false]);

        Livewire::test(SubscriptionBilling::class)
            ->set('plan_id', $goldPlan->id)
            ->set('billing_cycle', 'yearly')
            ->set('payment_method', 'card')
            ->call('processCardPayment')
            ->assertHasNoErrors();

        $company->refresh();
        $this->assertTrue($company->is_active);
        $this->assertEquals($goldPlan->id, $company->plan_id);
        $this->assertTrue($company->isSubscriptionActive());

        $this->assertDatabaseHas('subscription_requests', [
            'company_id' => $company->id,
            'plan_id' => $goldPlan->id,
            'billing_cycle' => 'yearly',
            'payment_method' => 'card',
            'status' => 'approved',
        ]);
    }

    public function test_subscription_page_renders_metallic_card_zero_lag_pricing_and_no_personal_email(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        Livewire::test(SubscriptionBilling::class)
            ->assertSuccessful()
            ->assertDontSee('pixelcraftstudioslk@gmail.com')
            ->assertSee('metallic-card-container')
            ->assertSee('metallic-emv-chip')
            ->assertSee('selectedPlanId')
            ->assertSee('billingCycle')
            ->assertSee('totalPayableLkr');
    }
}
