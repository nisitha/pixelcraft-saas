<?php

namespace Tests\Feature;

use App\Filament\App\Pages\Auth\Register;
use App\Filament\App\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\EnsureTenantHasActiveSubscription;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TenantOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_unauthenticated_user_accessing_app_new_is_redirected_to_login(): void
    {
        $response = $this->get('/app/new');
        $response->assertRedirect('/app/login');
    }

    public function test_unauthenticated_user_accessing_app_is_redirected_to_login(): void
    {
        $response = $this->get('/app');
        $response->assertRedirect('/app/login');
    }

    public function test_registration_requires_name_email_password_and_password_confirmation(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('app'));

        // 1. Assert validation errors when required fields are empty
        Livewire::test(Register::class)
            ->fillForm([
                'name' => '',
                'email' => '',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->call('register')
            ->assertHasFormErrors([
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);

        // 2. Assert error when password is less than 8 characters or confirmation mismatches
        Livewire::test(Register::class)
            ->fillForm([
                'name' => 'Valid Name',
                'email' => 'valid@example.com',
                'password' => 'short',
                'password_confirmation' => 'mismatch',
            ])
            ->call('register')
            ->assertHasFormErrors([
                'password',
            ]);

        // 3. Assert successful registration creates user with MustVerifyEmail
        Livewire::test(Register::class)
            ->fillForm([
                'name' => 'Kasun Perera',
                'email' => 'kasun@pixelcraftlk.com',
                'password' => 'Password@123',
                'password_confirmation' => 'Password@123',
            ])
            ->call('register')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'kasun@pixelcraftlk.com')->first();
        $this->assertNotNull($user);
        $this->assertInstanceOf(MustVerifyEmail::class, $user);
        $this->assertNull($user->email_verified_at);

        // 4. Assert that unverified user accessing /app/new is redirected to email verification prompt
        $this->actingAs($user);
        $response = $this->get('/app/new');
        $response->assertRedirect('/app/email-verification/prompt');
    }

    public function test_active_subscription_plans_are_fetched_and_displayed_during_company_registration(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $activePlans = SubscriptionPlan::where('is_active', true)->get();
        $this->assertGreaterThanOrEqual(3, $activePlans->count());

        $starterPlan = SubscriptionPlan::where('slug', 'starter')->first();
        $proPlan = SubscriptionPlan::where('slug', 'professional')->first();
        $enterprisePlan = SubscriptionPlan::where('slug', 'enterprise')->first();

        $this->assertNotNull($starterPlan);
        $this->assertNotNull($proPlan);
        $this->assertNotNull($enterprisePlan);

        Livewire::test(RegisterCompany::class)
            ->assertSee($starterPlan->name)
            ->assertSee($proPlan->name)
            ->assertSee($enterprisePlan->name);
    }

    public function test_company_creation_accurately_stores_subscription_plan_id_and_trial_ends_at(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $proPlan = SubscriptionPlan::where('slug', 'professional')->first();
        $this->assertNotNull($proPlan);

        Livewire::test(RegisterCompany::class)
            ->fillForm([
                'name' => 'Apex Cloud Solutions (Pvt) Ltd',
                'br_number' => 'PV-998877',
                'phone' => '+94 11 234 5678',
                'email' => 'info@apexcloud.lk',
                'currency' => 'LKR',
                'address' => 'No. 12, High Level Road, Nugegoda',
                'subscription_plan_id' => $proPlan->id,
            ])
            ->call('register')
            ->assertHasNoFormErrors();

        $company = Company::where('slug', 'apex-cloud-solutions-pvt-ltd')->first();
        $this->assertNotNull($company);
        $this->assertEquals('Apex Cloud Solutions (Pvt) Ltd', $company->name);
        $this->assertEquals($proPlan->id, $company->subscription_plan_id);
        $this->assertNotNull($company->trial_ends_at);
        $this->assertEquals(14, round(now()->diffInDays($company->trial_ends_at)));
        $this->assertEquals('trialing', $company->subscription_status);

        // Verify company relationship
        $this->assertNotNull($company->subscriptionPlan);
        $this->assertEquals($proPlan->id, $company->subscriptionPlan->id);

        // Verify user attached as owner
        $this->assertTrue($user->companies()->where('companies.id', $company->id)->exists());
        $this->assertEquals('owner', $user->roleInTenant($company));
    }

    public function test_expired_trial_middleware_blocks_operational_mutations_and_allows_billing(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $company = Company::create([
            'name' => 'Expired Trial Corp',
            'slug' => 'expired-trial-corp',
            'phone' => '+94 77 111 2222',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->subDay(),
            'subscription_ends_at' => now()->subDay(),
        ]);

        $company->users()->attach($user->id, ['role' => 'owner']);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        // 1. Subscription billing route must remain accessible
        $billingResponse = $this->get("/app/{$company->slug}/subscription-billing");
        $billingResponse->assertStatus(200);

        // 2. Operational state mutation (POST/PUT/DELETE) on non-billing module must be blocked (403)
        $request = Request::create("/app/{$company->slug}/contacts", 'POST');
        $middleware = new EnsureTenantHasActiveSubscription;

        try {
            $middleware->handle($request, fn () => new Response);
            $this->fail('Expected 403 HttpException was not thrown by EnsureTenantHasActiveSubscription.');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertStringContainsString('Subscription Expired / Free Trial Ended', $e->getMessage());
        }
    }

    public function test_trial_expiry_command_transitions_companies_and_logs_reminders(): void
    {
        // Company 1: Expired trial
        $expiredCompany = Company::create([
            'name' => 'Overdue Trial Co',
            'slug' => 'overdue-trial-co',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->subHour(),
        ]);

        // Company 2: Expiring in 2 days
        $expiringCompany = Company::create([
            'name' => 'Expiring Soon Co',
            'slug' => 'expiring-soon-co',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(2),
        ]);

        $this->artisan('subscription:check-trials')
            ->assertExitCode(0);

        $expiredCompany->refresh();
        $this->assertEquals('expired', $expiredCompany->subscription_status);

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $expiredCompany->id,
            'action' => 'trial_expired',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $expiringCompany->id,
            'action' => 'trial_expiry_reminder',
        ]);
    }
}
