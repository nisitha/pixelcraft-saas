<?php

namespace Tests\Feature;

use App\Filament\App\Pages\FinancialReports;
use App\Filament\App\Resources\Quotations\Pages\ViewQuotation;
use App\Filament\App\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Filament\App\Resources\TeamMembers\Pages\EditTeamMember;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class TenantStaffRolesAndPackagePdfTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $owner;

    protected User $admin;

    protected User $salesExec;

    protected User $cashier;

    protected User $engineer;

    protected Contact $contact;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->company = Company::where('slug', 'lanka-solar-solutions')->first();
        $this->owner = User::where('email', 'owner@lankasolar.lk')->first();
        $this->contact = Contact::where('company_id', $this->company->id)->first();

        // Create standard test product
        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Vertex 580W Bifacial Solar Panel',
            'sku' => 'V-580W-BF',
            'cost_price' => 22000.00,
            'selling_price' => 38000.00,
            'stock_quantity' => 50,
            'unit' => 'pcs',
            'warranty_months' => 360,
        ]);

        // Create Admin user
        $this->admin = User::create([
            'name' => 'Operations Manager',
            'email' => 'admin@lankasolar.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->admin->companies()->attach($this->company->id, ['role' => 'admin']);

        // Create Sales Executive user
        $this->salesExec = User::create([
            'name' => 'Sales Executive',
            'email' => 'sales@lankasolar.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->salesExec->companies()->attach($this->company->id, ['role' => 'sales_executive']);

        // Create Cashier user
        $this->cashier = User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@lankasolar.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->cashier->companies()->attach($this->company->id, ['role' => 'cashier']);

        // Create Engineer user
        $this->engineer = User::create([
            'name' => 'Site Engineer',
            'email' => 'engineer@lankasolar.lk',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $this->engineer->companies()->attach($this->company->id, ['role' => 'engineer']);

        $this->actingAs($this->owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    public function test_tenant_roles_and_financial_permission_helpers(): void
    {
        // Owner checks
        $this->assertTrue($this->owner->isTenantOwner($this->company));
        $this->assertTrue($this->owner->canViewFinancialProfitAndCost($this->company));
        $this->assertTrue($this->owner->canDeleteTenantRecords($this->company));

        // Admin checks
        $this->assertTrue($this->admin->isTenantAdmin($this->company));
        $this->assertTrue($this->admin->canViewFinancialProfitAndCost($this->company));
        $this->assertTrue($this->admin->canDeleteTenantRecords($this->company));

        // Sales Executive checks
        $this->assertTrue($this->salesExec->isTenantSalesExecutive($this->company));
        $this->assertFalse($this->salesExec->canViewFinancialProfitAndCost($this->company));
        $this->assertFalse($this->salesExec->canDeleteTenantRecords($this->company));

        // Cashier checks
        $this->assertTrue($this->cashier->isTenantCashier($this->company));
        $this->assertFalse($this->cashier->canViewFinancialProfitAndCost($this->company));
        $this->assertFalse($this->cashier->canDeleteTenantRecords($this->company));

        // Engineer checks
        $this->assertTrue($this->engineer->isTenantEngineer($this->company));
        $this->assertFalse($this->engineer->canViewFinancialProfitAndCost($this->company));
        $this->assertFalse($this->engineer->canDeleteTenantRecords($this->company));
    }

    public function test_staff_role_policy_enforcement(): void
    {
        // Expenses: allowed for Owner/Admin, forbidden for Sales, Cashier, Engineer
        $this->actingAs($this->owner);
        Filament::setTenant($this->company);
        $this->assertTrue(Gate::allows('viewAny', Expense::class));
        $this->assertTrue(Gate::allows('create', Expense::class));

        $this->actingAs($this->admin);
        Filament::setTenant($this->company);
        $this->assertTrue(Gate::allows('viewAny', Expense::class));
        $this->assertTrue(Gate::allows('create', Expense::class));

        $this->actingAs($this->salesExec);
        Filament::setTenant($this->company);
        $this->assertFalse(Gate::allows('viewAny', Expense::class));
        $this->assertFalse(Gate::allows('create', Expense::class));

        $this->actingAs($this->cashier);
        Filament::setTenant($this->company);
        $this->assertFalse(Gate::allows('viewAny', Expense::class));
        $this->assertFalse(Gate::allows('create', Expense::class));

        $this->actingAs($this->engineer);
        Filament::setTenant($this->company);
        $this->assertFalse(Gate::allows('viewAny', Expense::class));
        $this->assertFalse(Gate::allows('create', Expense::class));

        // Payments: Cashier can create/view, Engineer is forbidden
        $this->actingAs($this->cashier);
        Filament::setTenant($this->company);
        $this->assertTrue(Gate::allows('create', Payment::class));
        $this->assertTrue(Gate::allows('viewAny', Payment::class));

        $this->actingAs($this->engineer);
        Filament::setTenant($this->company);
        $this->assertFalse(Gate::allows('create', Payment::class));
        $this->assertFalse(Gate::allows('viewAny', Payment::class));

        // Financial Reports Page: Owner/Admin can access, others cannot
        $this->actingAs($this->owner);
        Filament::setTenant($this->company);
        $this->assertTrue(FinancialReports::canAccess());

        $this->actingAs($this->salesExec);
        Filament::setTenant($this->company);
        $this->assertFalse(FinancialReports::canAccess());

        $this->actingAs($this->cashier);
        Filament::setTenant($this->company);
        $this->assertFalse(FinancialReports::canAccess());

        $this->actingAs($this->engineer);
        Filament::setTenant($this->company);
        $this->assertFalse(FinancialReports::canAccess());
    }

    public function test_company_max_users_limit_enforcement(): void
    {
        $plan = Plan::create([
            'name' => 'Single User Plan',
            'slug' => 'single-user-plan',
            'price_lkr' => 5000,
            'max_users' => 1,
            'max_quotations_month' => 50,
            'max_invoices_month' => 50,
            'features' => [],
            'is_active' => true,
        ]);

        $company = Company::create([
            'name' => 'Solo Solar Tech',
            'slug' => 'solo-solar-tech',
            'plan_id' => $plan->id,
            'status' => 'active',
        ]);
        $company->users()->attach($this->owner->id, ['role' => 'owner']);

        $this->assertEquals(1, $company->getMaxUsersLimit());
        // Since company has 1 user and max_users is 1, canAddMoreUsers should return false
        $this->assertFalse($company->canAddMoreUsers());
    }

    public function test_dual_pdf_quotation_package_mode_vs_itemized_mode(): void
    {
        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-DUAL-001',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'subtotal' => 380000.00,
            'discount' => 10000.00,
            'tax' => 0.00,
            'grand_total' => 370000.00,
            'status' => 'draft',
            'price_display_mode' => 'package_total_only',
        ]);

        // Visible item with warranty
        $quotation->items()->create([
            'product_id' => $this->product->id,
            'description' => 'Vertex 580W Panels (10 Pcs)',
            'quantity' => 10,
            'unit_price' => 35000.00,
            'tax' => 0.00,
            'subtotal' => 350000.00,
            'warranty_text' => '30 Years Performance Warranty',
            'hide_on_customer_pdf' => false,
        ]);

        // Internal accessory row hidden from customer PDF
        $quotation->items()->create([
            'product_id' => null,
            'description' => 'Internal Cabling & Transport Allowance',
            'quantity' => 1,
            'unit_price' => 30000.00,
            'tax' => 0.00,
            'subtotal' => 30000.00,
            'warranty_text' => null,
            'hide_on_customer_pdf' => true,
        ]);

        $pdfService = app(PdfService::class);

        // 1. Package Mode:
        $packagePdfHtml = view('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $this->company,
            'contact' => $this->contact,
            'items' => $quotation->items,
            'mode' => 'package_total_only',
        ])->render();

        // Must show items, quantity, warranty, discount and grand total
        $this->assertStringContainsString('Vertex 580W Panels', $packagePdfHtml);
        $this->assertStringContainsString('30 Years Performance Warranty', $packagePdfHtml);
        $this->assertStringContainsString('Total Package Amount', $packagePdfHtml);
        $this->assertStringContainsString('370,000.00', $packagePdfHtml);

        // Must hide the internal accessory row marked hide_on_customer_pdf
        $this->assertStringNotContainsString('Internal Cabling & Transport Allowance', $packagePdfHtml);

        // Must NOT render unit price or Rate header in the table
        $this->assertStringNotContainsString('Unit Price (Rs.)', $packagePdfHtml);
        $this->assertStringNotContainsString('35,000.00', $packagePdfHtml);

        // 2. Itemized Mode:
        $itemizedPdfHtml = view('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $this->company,
            'contact' => $this->contact,
            'items' => $quotation->items,
            'mode' => 'itemized',
        ])->render();

        // Must show unit prices and rates
        $this->assertStringContainsString('Unit Price (Rs.)', $itemizedPdfHtml);
        $this->assertStringContainsString('35,000.00', $itemizedPdfHtml);
        $this->assertStringContainsString('Grand Total:', $itemizedPdfHtml);
    }

    public function test_dual_pdf_invoice_package_mode_vs_itemized_mode(): void
    {
        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-DUAL-001',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'type' => 'milestone',
            'subtotal' => 380000.00,
            'discount' => 10000.00,
            'tax' => 0.00,
            'grand_total' => 370000.00,
            'paid_amount' => 100000.00,
            'due_amount' => 270000.00,
            'status' => 'partial',
            'price_display_mode' => 'package_total_only',
        ]);

        $invoice->items()->create([
            'product_id' => $this->product->id,
            'description' => 'Vertex 580W Panels (10 Pcs)',
            'quantity' => 10,
            'unit_price' => 38000.00,
            'tax' => 0.00,
            'subtotal' => 380000.00,
            'warranty_text' => '10 Years Replacement Warranty',
            'hide_on_customer_pdf' => false,
        ]);

        // 1. Package Mode:
        $packagePdfHtml = view('pdf.invoice', [
            'invoice' => $invoice,
            'company' => $this->company,
            'contact' => $this->contact,
            'items' => $invoice->items,
            'quotation' => null,
            'payments' => collect([]),
            'mode' => 'package_total_only',
        ])->render();

        $this->assertStringContainsString('10 Years Replacement Warranty', $packagePdfHtml);
        $this->assertStringContainsString('Total Package Amount', $packagePdfHtml);
        $this->assertStringNotContainsString('Unit Price (Rs.)', $packagePdfHtml);
        $this->assertStringNotContainsString('38,000.00', $packagePdfHtml);

        // 2. Itemized Mode:
        $itemizedPdfHtml = view('pdf.invoice', [
            'invoice' => $invoice,
            'company' => $this->company,
            'contact' => $this->contact,
            'items' => $invoice->items,
            'quotation' => null,
            'payments' => collect([]),
            'mode' => 'itemized',
        ])->render();

        $this->assertStringContainsString('Unit Price (Rs.)', $itemizedPdfHtml);
        $this->assertStringContainsString('38,000.00', $itemizedPdfHtml);
        $this->assertStringContainsString('Invoice Grand Total:', $itemizedPdfHtml);
    }

    public function test_split_view_livewire_component_mode_switching(): void
    {
        $this->actingAs($this->owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);

        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-SPLIT-TOGGLE',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'subtotal' => 200000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 200000.00,
            'status' => 'draft',
            'price_display_mode' => 'itemized',
        ]);

        $quotation->items()->create([
            'product_id' => $this->product->id,
            'description' => 'Solar Module',
            'quantity' => 5,
            'unit_price' => 40000.00,
            'tax' => 0.00,
            'subtotal' => 200000.00,
            'warranty_text' => '25 Years Warranty',
        ]);

        Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
            ->assertSee('Standard Itemized View')
            ->call('togglePreviewPriceDisplayMode')
            ->assertSet('previewMode', 'package_total_only')
            ->assertSee('Customer Package View');
    }

    public function test_team_member_creation_saves_user_and_attaches_to_tenant_with_role(): void
    {
        $this->actingAs($this->owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);

        Livewire::test(CreateTeamMember::class)
            ->fillForm([
                'name' => 'Nimal Perera',
                'email' => 'nimal@example.com',
                'password' => 'secret12345',
                'role' => 'engineer',
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'nimal@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Nimal Perera', $user->name);
        $this->assertTrue(Hash::check('secret12345', $user->password));

        // Check pivot
        $this->assertTrue($this->company->users()->where('users.id', $user->id)->exists());
        $this->assertSame('engineer', $user->roleInTenant($this->company));
    }

    public function test_team_member_editing_updates_role_on_tenant_pivot(): void
    {
        $this->actingAs($this->owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);

        Livewire::test(EditTeamMember::class, ['record' => $this->salesExec->id])
            ->assertFormSet([
                'role' => 'sales_executive',
                'name' => 'Sales Executive',
            ])
            ->fillForm([
                'role' => 'cashier',
                'name' => 'Senior Cashier',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->salesExec->refresh();
        $this->assertSame('Senior Cashier', $this->salesExec->name);
        $this->assertSame('cashier', $this->salesExec->roleInTenant($this->company));
    }
}
