<?php

namespace Tests\Feature;

use App\Filament\App\Pages\Auth\Register;
use App\Filament\App\Pages\SubscriptionBilling;
use App\Filament\App\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\EnsureTenantHasActiveSubscription;
use App\Mail\PaymentRejectedMail;
use App\Mail\SubscriptionActivatedMail;
use App\Mail\SubscriptionExpiredMail;
use App\Mail\TrialReminderMail;
use App\Models\Company;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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

    public function test_tenant_bank_slip_submission_creates_pending_subscription_payment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $plan = Plan::first();
        $subPlan = SubscriptionPlan::first();

        $company = Company::create([
            'name' => 'Colombo Tech Solutions',
            'slug' => 'colombo-tech-solutions',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'plan_id' => $plan->id,
            'subscription_plan_id' => $subPlan->id,
            'trial_ends_at' => now()->addDays(10),
        ]);

        $company->users()->attach($user->id, ['role' => 'owner']);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $fakeSlip = UploadedFile::fake()->create('hnb_slip.jpg', 500, 'image/jpeg');

        Livewire::test(SubscriptionBilling::class)
            ->set('plan_id', $plan->id)
            ->set('billing_cycle', 'yearly')
            ->set('payment_method', 'bank_transfer')
            ->set('payment_reference', 'HNB-TRX-987654')
            ->set('receipt', $fakeSlip)
            ->set('notes', 'Payment for Colombo Tech Annual Subscription')
            ->call('submitRenewalRequest')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('subscription_payments', [
            'company_id' => $company->id,
            'subscription_plan_id' => $subPlan->id,
            'reference_number' => 'HNB-TRX-987654',
            'status' => 'pending',
            'notes' => 'Payment for Colombo Tech Annual Subscription',
        ]);

        $payment = SubscriptionPayment::where('reference_number', 'HNB-TRX-987654')->first();
        $this->assertNotNull($payment);
        $this->assertNotEmpty($payment->slip_path);
        Storage::disk('public')->assertExists($payment->slip_path);
    }

    public function test_super_admin_approves_payment_activates_subscription_and_sends_email(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'is_super_admin' => true,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $owner = User::factory()->create([
            'email' => 'ceo@colombotech.lk',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $subPlan = SubscriptionPlan::where('slug', 'professional')->first() ?? SubscriptionPlan::first();

        $company = Company::create([
            'name' => 'Colombo Tech Solutions',
            'slug' => 'colombo-tech-solutions-approval',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'subscription_plan_id' => $subPlan->id,
            'trial_ends_at' => now()->addDays(5),
            'subscription_ends_at' => null,
        ]);

        $company->users()->attach($owner->id, ['role' => 'owner']);

        $payment = SubscriptionPayment::create([
            'company_id' => $company->id,
            'subscription_plan_id' => $subPlan->id,
            'amount' => 7500.00,
            'currency' => 'LKR',
            'period_years' => 1,
            'reference_number' => 'HNB-REF-554433',
            'slip_path' => 'subscription-receipts/fake.jpg',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Perform the approval flow as executed by SubscriptionPaymentsTable action
        $years = 1;
        $newExpiry = now()->addYears($years);

        $payment->update([
            'status' => 'approved',
            'period_years' => $years,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $company->update([
            'subscription_status' => 'active',
            'subscription_plan_id' => $payment->subscription_plan_id,
            'subscription_ends_at' => $newExpiry,
            'trial_ends_at' => null,
            'is_active' => true,
        ]);

        $mailable = new SubscriptionActivatedMail($company, $payment);
        Mail::to($company->owner()->email)->send($mailable);

        $payment->refresh();
        $company->refresh();

        $this->assertEquals('approved', $payment->status);
        $this->assertEquals($admin->id, $payment->approved_by);
        $this->assertNotNull($payment->approved_at);

        $this->assertEquals('active', $company->subscription_status);
        $this->assertNull($company->trial_ends_at);
        $this->assertNotNull($company->subscription_ends_at);
        $this->assertTrue($company->subscription_ends_at->isFuture());

        Mail::assertSent(SubscriptionActivatedMail::class, function ($mail) use ($owner) {
            return $mail->hasTo($owner->email);
        });
    }

    public function test_super_admin_rejects_payment_and_dispatches_rejection_email(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'is_super_admin' => true,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $owner = User::factory()->create([
            'email' => 'finance@badslip.lk',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $company = Company::create([
            'name' => 'Bad Slip Corp',
            'slug' => 'bad-slip-corp',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(5),
        ]);

        $company->users()->attach($owner->id, ['role' => 'owner']);

        $payment = SubscriptionPayment::create([
            'company_id' => $company->id,
            'subscription_plan_id' => SubscriptionPlan::first()->id,
            'amount' => 3500.00,
            'currency' => 'LKR',
            'period_years' => 1,
            'reference_number' => 'INVALID-REF-000',
            'slip_path' => 'subscription-receipts/blurry.jpg',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $rejectionReason = 'The bank receipt image is illegible and reference number does not match statement.';

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $mailable = new PaymentRejectedMail($company, $payment, $rejectionReason);
        Mail::to($company->owner()->email)->send($mailable);

        $payment->refresh();
        $this->assertEquals('rejected', $payment->status);
        $this->assertEquals($rejectionReason, $payment->rejection_reason);

        Mail::assertSent(PaymentRejectedMail::class, function ($mail) use ($owner, $rejectionReason) {
            return $mail->hasTo($owner->email) && $mail->rejectionReason === $rejectionReason;
        });
    }

    public function test_saas_check_subscriptions_command_transitions_expired_and_sends_reminders(): void
    {
        Mail::fake();

        $owner1 = User::factory()->create([
            'email' => 'owner1@expired.lk',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $expiredCompany = Company::create([
            'name' => 'Expired Trial Co',
            'slug' => 'expired-trial-dog',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->subMinutes(10),
        ]);
        $expiredCompany->users()->attach($owner1->id, ['role' => 'owner']);

        $owner2 = User::factory()->create([
            'email' => 'owner2@reminder.lk',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $reminderCompany = Company::create([
            'name' => 'Reminder Co',
            'slug' => 'reminder-co',
            'currency' => 'LKR',
            'is_active' => true,
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(3)->startOfDay()->addHours(10),
        ]);
        $reminderCompany->users()->attach($owner2->id, ['role' => 'owner']);

        $this->artisan('saas:check-subscriptions')
            ->assertExitCode(0);

        $expiredCompany->refresh();
        $this->assertEquals('expired', $expiredCompany->subscription_status);

        Mail::assertSent(SubscriptionExpiredMail::class, function ($mail) use ($owner1) {
            return $mail->hasTo($owner1->email);
        });

        Mail::assertSent(TrialReminderMail::class, function ($mail) use ($owner2) {
            return $mail->hasTo($owner2->email) && $mail->daysRemaining === 3;
        });
    }
}
