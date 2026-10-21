<?php

namespace Tests\Feature;

use App\Filament\App\Pages\FinancialReports;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialReportsOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->company = Company::where('slug', 'lanka-solar-solutions')->firstOrFail();

        $this->user = User::firstOrCreate(
            ['email' => 'admin_opt@lankasolar.lk'],
            ['name' => 'Admin Opt User', 'password' => Hash::make('Password@123'), 'is_active' => true]
        );

        if (! $this->company->users()->where('user_id', $this->user->id)->exists()) {
            $this->company->users()->attach($this->user->id, ['role' => 'owner']);
        }

        $this->actingAs($this->user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    public function test_direct_sql_aggregations_and_micro_caching(): void
    {
        $tenantId = $this->company->id;
        FinancialReports::flushCacheForTenant($tenantId);

        $customer = Contact::create([
            'company_id' => $tenantId,
            'name' => 'Solar Customer Alpha',
            'phone' => '0771234567',
        ]);

        $product = Product::create([
            'company_id' => $tenantId,
            'name' => 'Solar Panel 500W',
            'sku' => 'PNL-OPT-500',
            'unit_price' => 50000.00,
            'cost_price' => 30000.00,
        ]);

        $invoice = Invoice::create([
            'company_id' => $tenantId,
            'contact_id' => $customer->id,
            'invoice_number' => 'INV-OPT-100',
            'invoice_date' => now()->toDateString(),
            'subtotal' => 100000.00,
            'discount' => 5000.00,
            'tax' => 0.00,
            'grand_total' => 95000.00,
            'paid_amount' => 45000.00,
            'due_amount' => 50000.00,
            'status' => 'partial',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'description' => '2 x 500W Solar Panels',
            'quantity' => 2,
            'unit_price' => 50000.00,
            'cost_price' => 30000.00,
            'subtotal' => 100000.00,
        ]);

        $payment = Payment::create([
            'company_id' => $tenantId,
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => 45000.00,
            'method' => 'bank_transfer',
        ]);

        $expense = Expense::create([
            'company_id' => $tenantId,
            'category' => 'Transport',
            'title' => 'Delivery to site',
            'amount' => 5000.00,
            'expense_date' => now()->toDateString(),
        ]);

        $reports = new FinancialReports;
        $reports->startDate = '2020-01-01';
        $reports->endDate = now()->addYear()->toDateString();
        $reports->period = 'all';

        // 1. Initial calculation and cache population
        $metrics = $reports->getFinancialMetrics();
        $this->assertGreaterThanOrEqual(95000.00, $metrics['invoiced_sales']);
        $this->assertGreaterThanOrEqual(45000.00, $metrics['payments_collected']);
        $this->assertGreaterThanOrEqual(60000.00, $metrics['cogs']);
        $this->assertGreaterThanOrEqual(5000.00, $metrics['operating_expenses']);

        // 2. Cache key check
        $cacheKey = FinancialReports::getCacheKey($tenantId, 'all', $reports->startDate, $reports->endDate);
        $this->assertTrue(Cache::has($cacheKey), 'Aggregated financial metrics should be cached under tenant key');

        // 3. Observer Invalidation on Payment creation
        $payment2 = Payment::create([
            'company_id' => $tenantId,
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => 10000.00,
            'method' => 'cash',
        ]);

        $this->assertFalse(Cache::has($cacheKey), 'Payment creation must flush tenant financial report cache');

        // 4. Observer Invalidation on Expense creation
        $reports->getFinancialMetrics();
        $this->assertTrue(Cache::has($cacheKey));

        Expense::create([
            'company_id' => $tenantId,
            'category' => 'Food',
            'title' => 'Crew lunch',
            'amount' => 1500.00,
            'expense_date' => now()->toDateString(),
        ]);

        $this->assertFalse(Cache::has($cacheKey), 'Expense creation must flush tenant financial report cache');

        // 5. Observer Invalidation on Invoice cancellation/update
        $reports->getFinancialMetrics();
        $this->assertTrue(Cache::has($cacheKey));

        $invoice->update(['status' => 'cancelled']);
        $this->assertFalse(Cache::has($cacheKey), 'Invoice status update must flush tenant financial report cache');
    }

    public function test_livewire_page_rendering_and_lightweight_state(): void
    {
        $component = Livewire::test(FinancialReports::class)
            ->assertSuccessful()
            ->assertViewHas('metrics')
            ->assertViewHas('pnl')
            ->assertViewHas('availableInvoices');

        $this->assertEquals('all', $component->get('period'));
        $this->assertNotNull($component->get('startDate'));
        $this->assertNotNull($component->get('endDate'));

        // Verify collection getters return Illuminate\Support\Collection and no __PHP_Incomplete_Class
        $breakdown = $component->instance()->getExpenseBreakdown();
        $this->assertInstanceOf(Collection::class, $breakdown);

        $receivables = $component->instance()->getReceivablesSummary();
        $this->assertInstanceOf(Collection::class, $receivables);
    }
}
