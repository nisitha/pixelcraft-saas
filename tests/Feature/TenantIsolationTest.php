<?php

namespace Tests\Feature;

use App\Filament\App\Pages\Tenancy\RegisterCompany;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test 1: User can register and create a company via onboarding.
     */
    public function test_user_can_register_and_create_company_via_onboarding(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $silverPlan = Plan::where('slug', 'silver')->first();

        Livewire::test(RegisterCompany::class)
            ->fillForm([
                'name' => 'PixelCraft Innovations',
                'br_number' => 'PV-889977',
                'phone' => '+94 77 123 4567',
                'currency' => 'LKR',
                'email' => 'contact@pixelcraft.lk',
                'address' => 'No. 100, High Level Road, Colombo 05',
                'plan_id' => $silverPlan?->id,
            ])
            ->call('register')
            ->assertHasNoFormErrors();

        $company = Company::where('slug', 'pixelcraft-innovations')->first();
        $this->assertNotNull($company);
        $this->assertEquals('PixelCraft Innovations', $company->name);
        $this->assertEquals('PV-889977', $company->br_number);
        $this->assertEquals('+94 77 123 4567', $company->phone);
        $this->assertEquals('contact@pixelcraft.lk', $company->email);
        $this->assertEquals('LKR', $company->currency);
        $this->assertTrue($company->is_active);

        // Verify user is attached as owner in company_user pivot
        $this->assertTrue($user->companies()->where('companies.id', $company->id)->exists());
        $pivot = $user->companies()->where('companies.id', $company->id)->first()->pivot;
        $this->assertEquals('owner', $pivot->role);

        // Verify user can access tenant
        $this->assertTrue($user->canAccessTenant($company));
        $this->assertTrue($user->getTenants(Filament::getPanel('app'))->contains('id', $company->id));
    }

    /**
     * Test 2: User A belonging to Company A cannot view or query Company B's invoices or employees.
     */
    public function test_user_a_cannot_view_or_query_company_b_invoices_or_employees(): void
    {
        // Setup Company A & User A
        $companyA = Company::factory()->create([
            'name' => 'Company Alpha',
            'slug' => 'company-alpha',
            'currency' => 'LKR',
            'is_active' => true,
        ]);

        $userA = User::factory()->create([
            'is_super_admin' => false,
            'is_active' => true,
        ]);
        $companyA->users()->attach($userA->id, ['role' => 'owner']);

        // Setup Company B & User B
        $companyB = Company::factory()->create([
            'name' => 'Company Beta',
            'slug' => 'company-beta',
            'currency' => 'USD',
            'is_active' => true,
        ]);

        $userB = User::factory()->create([
            'is_super_admin' => false,
            'is_active' => true,
        ]);
        $companyB->users()->attach($userB->id, ['role' => 'owner']);

        // Create Contacts for each Company
        $contactA = Contact::create([
            'company_id' => $companyA->id,
            'name' => 'Customer Alpha',
            'type' => 'customer',
            'phone' => '+94771112233',
        ]);

        $contactB = Contact::create([
            'company_id' => $companyB->id,
            'name' => 'Customer Beta',
            'type' => 'customer',
            'phone' => '+94772223344',
        ]);

        // Create Records for Company A
        $invoiceA = Invoice::create([
            'company_id' => $companyA->id,
            'contact_id' => $contactA->id,
            'invoice_number' => 'INV-ALPHA-001',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'type' => 'standard',
            'subtotal' => 100000.00,
            'grand_total' => 100000.00,
            'paid_amount' => 100000.00,
            'due_amount' => 0.00,
            'status' => 'paid',
        ]);

        $employeeA = Employee::create([
            'company_id' => $companyA->id,
            'name' => 'Employee Alpha',
            'nic_number' => '199011111111',
            'designation' => 'Developer',
            'contact_number' => '+94770000001',
            'basic_salary' => 75000.00,
            'fixed_allowance' => 10000.00,
            'joined_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        // Create Records for Company B
        $invoiceB = Invoice::create([
            'company_id' => $companyB->id,
            'contact_id' => $contactB->id,
            'invoice_number' => 'INV-BETA-001',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'type' => 'standard',
            'subtotal' => 200000.00,
            'grand_total' => 200000.00,
            'paid_amount' => 200000.00,
            'due_amount' => 0.00,
            'status' => 'paid',
        ]);

        $employeeB = Employee::create([
            'company_id' => $companyB->id,
            'name' => 'Employee Beta',
            'nic_number' => '199022222222',
            'designation' => 'Designer',
            'contact_number' => '+94770000002',
            'basic_salary' => 85000.00,
            'fixed_allowance' => 15000.00,
            'joined_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        // Assertion 1: Model-level tenant authorization
        $this->assertTrue($userA->canAccessTenant($companyA));
        $this->assertFalse($userA->canAccessTenant($companyB));

        $this->assertTrue($userB->canAccessTenant($companyB));
        $this->assertFalse($userB->canAccessTenant($companyA));

        // Assertion 2: Query Scoping per Company
        $this->assertEquals(1, $companyA->invoices()->count());
        $this->assertEquals('INV-ALPHA-001', $companyA->invoices()->first()->invoice_number);
        $this->assertFalse($companyA->invoices()->where('id', $invoiceB->id)->exists());

        $this->assertEquals(1, $companyA->employees()->count());
        $this->assertEquals('Employee Alpha', $companyA->employees()->first()->name);
        $this->assertFalse($companyA->employees()->where('id', $employeeB->id)->exists());

        $this->assertEquals(1, $companyB->invoices()->count());
        $this->assertEquals('INV-BETA-001', $companyB->invoices()->first()->invoice_number);
        $this->assertFalse($companyB->invoices()->where('id', $invoiceA->id)->exists());

        $this->assertEquals(1, $companyB->employees()->count());
        $this->assertEquals('Employee Beta', $companyB->employees()->first()->name);
        $this->assertFalse($companyB->employees()->where('id', $employeeA->id)->exists());

        // Assertion 3: HTTP Access Control across tenant boundaries
        // User A accessing Company A tenant panel URL is allowed
        $responseA = $this->actingAs($userA)->get("/app/{$companyA->slug}");
        $this->assertTrue(in_array($responseA->status(), [200, 302]));

        // User A attempting to access Company B tenant panel URL is forbidden / unauthorized
        $responseB = $this->actingAs($userA)->get("/app/{$companyB->slug}");
        $this->assertTrue(in_array($responseB->status(), [403, 404]));
    }

    /**
     * Test 3: Normal user cannot access /admin; only a user with is_super_admin = true can access /admin.
     */
    public function test_normal_user_cannot_access_admin_panel_only_super_admin_can(): void
    {
        $adminPanel = Filament::getPanel('admin');

        $normalUser = User::factory()->create([
            'is_super_admin' => false,
            'is_active' => true,
        ]);

        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        // Method-level checks
        $this->assertFalse($normalUser->canAccessPanel($adminPanel));
        $this->assertTrue($superAdmin->canAccessPanel($adminPanel));

        // Unauthenticated access redirects to login
        $this->get('/admin')->assertRedirect('/admin/login');

        // Normal user accessing /admin is forbidden
        $this->actingAs($normalUser)
            ->get('/admin')
            ->assertForbidden();

        // Super Admin accessing /admin is authorized
        $response = $this->actingAs($superAdmin)->get('/admin');
        $this->assertTrue(in_array($response->status(), [200, 302]));
    }
}
