<?php

namespace Tests\Feature;

use App\Filament\App\Pages\FinancialReports;
use App\Filament\App\Resources\Payments\Pages\ListPayments;
use App\Filament\App\Widgets\PaymentsOverviewWidget;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\GaminiProjectDataSeeder;
use Database\Seeders\VertexHomesProjectDataSeeder;
use Database\Seeders\VertexHomesSeeder;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class VertexHomesProfitAndPaymentsReactivityTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Vertex Homes base company and both project datasets
        $this->seed(VertexHomesSeeder::class);
        $this->seed(VertexHomesProjectDataSeeder::class);
        $this->seed(GaminiProjectDataSeeder::class);

        $this->company = Company::where('slug', 'vertex-homes')->firstOrFail();

        $this->user = User::firstOrCreate(
            ['email' => 'director@vertexhomes.lk'],
            ['name' => 'Managing Director', 'password' => Hash::make('Password@123'), 'is_active' => true]
        );

        if (! $this->company->users()->where('user_id', $this->user->id)->exists()) {
            $this->company->users()->attach($this->user->id, ['role' => 'owner']);
        }

        $this->actingAs($this->user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);

        FinancialReports::flushCacheForTenant($this->company->id);
    }

    public function test_all_time_financial_reports_evaluates_to_true_net_profit_without_double_deduction(): void
    {
        $reports = new FinancialReports;
        $reports->period = 'all';
        $reports->startDate = '2020-01-01';
        $reports->endDate = '2030-12-31';

        $metrics = $reports->getFinancialMetrics();

        // 1. Invoiced Sales: Fernando (1,250,000) + Gamini (1,900,000) = 3,150,000.00
        $this->assertEquals(3150000.00, $metrics['invoiced_sales']);

        // 2. Cost of Goods Sold (COGS from invoice items):
        // Fernando Items COGS: 1,002,799.00
        // Gamini Items COGS: 1,683,592.50
        // Total COGS = 2,686,391.50
        $this->assertEquals(2686391.50, $metrics['cogs']);

        // 3. Gross Profit = 3,150,000 - 2,686,391.50 = 463,608.50
        $this->assertEquals(463608.50, $metrics['gross_profit']);

        // 4. Sales Commissions: Fernando (37,500) + Gamini (57,000) = 94,500.00
        $this->assertEquals(94500.00, $metrics['agent_commissions']);

        // 5. Operating Expenses: External supplier hardware purchases already in COGS are NOT double deducted!
        // No unlinked external operating expenses for Vertex Homes: 0.00
        $this->assertEquals(0.00, $metrics['operating_expenses']);

        // 6. Company Absorbed Overhead: Rs. 112,000 x 2 = Rs. 224,000.00 absorbed into company retained funds
        $this->assertEquals(224000.00, $metrics['internal_overhead']);

        // 7. Total True Retained Profit:
        // Fernando Net Profit: +LKR 209,701.00
        // Gamini Net Profit: +LKR 159,407.50
        // Total True Retained Profit = +LKR 369,108.50
        $this->assertEquals(369108.50, $metrics['net_profit']);
        $this->assertGreaterThan(0, $metrics['net_profit']);

        // 8. Verify Individual Project Costing Explorer
        // Fernando: INV-2026-0005
        $fernandoInvoice = $this->company->invoices()->where('invoice_number', 'INV-2026-0005')->firstOrFail();
        $reports->selectedInvoiceId = $fernandoInvoice->id;
        $fernandoPnl = $reports->getInvoiceProfitabilityMetrics();

        $this->assertTrue($fernandoPnl['has_data']);
        $this->assertEquals(1250000.00, $fernandoPnl['contract_value']);
        $this->assertEquals(209701.00, $fernandoPnl['net_profit'], 'Fernando Net Profit must be exactly +LKR 209,701.00');
        $this->assertEquals(112000.00, $fernandoPnl['internal_company_overhead']);

        // Gamini: VHE/2026/00179
        $gaminiInvoice = $this->company->invoices()->where('invoice_number', 'VHE/2026/00179')->firstOrFail();
        $reports->selectedInvoiceId = $gaminiInvoice->id;
        $gaminiPnl = $reports->getInvoiceProfitabilityMetrics();

        $this->assertTrue($gaminiPnl['has_data']);
        $this->assertEquals(1900000.00, $gaminiPnl['contract_value']);
        $this->assertEquals(159407.50, $gaminiPnl['net_profit'], 'Gamini Net Profit must be exactly +LKR 159,407.50');
        $this->assertEquals(112000.00, $gaminiPnl['internal_company_overhead']);
    }

    public function test_payments_overview_widget_uses_interacts_with_page_table_trait(): void
    {
        $traits = class_uses_recursive(PaymentsOverviewWidget::class);
        $this->assertArrayHasKey(
            InteractsWithPageTable::class,
            $traits,
            'PaymentsOverviewWidget must implement Filament\Widgets\Concerns\InteractsWithPageTable trait'
        );
    }

    public function test_payments_overview_widget_reacts_dynamically_to_active_table_query(): void
    {
        // Total payments seeded: 2 Fernando (625,000 x 2 = 1,250,000) + 3 Gamini (900,000 + 525,000 + 475,000 = 1,900,000)
        // Total payments = 5, Total amount = 3,150,000.00, all bank_transfer
        $this->assertEquals(5, Payment::where('company_id', $this->company->id)->count());

        $widget = Livewire::test(PaymentsOverviewWidget::class);

        $viewData = $widget->instance()->getViewData();
        $this->assertEquals(3150000.00, $viewData['totalCollected']);
        $this->assertEquals(5, $viewData['transactionsCount']);
        $this->assertEquals(3150000.00, $viewData['bankTransferTotal']);
        $this->assertEquals(0.00, $viewData['cashPosTotal']);
        $this->assertEquals(100.0, $viewData['bankPercentage']);
        $this->assertEquals(0.0, $viewData['cashPercentage']);

        // Filter by Customer: Mr. Fernando
        $fernando = Contact::where('company_id', $this->company->id)
            ->where('name', 'like', '%Fernando%')
            ->firstOrFail();

        $widgetFernando = Livewire::test(PaymentsOverviewWidget::class, [
            'tableFilters' => ['customer' => ['value' => $fernando->id]],
        ]);

        $filteredFernando = $widgetFernando->instance()->getViewData();
        $this->assertEquals(1250000.00, $filteredFernando['totalCollected'], 'Total collected must match Fernando payments');
        $this->assertEquals(2, $filteredFernando['transactionsCount']);
        $this->assertEquals(1250000.00, $filteredFernando['bankTransferTotal']);

        // Filter by Customer: Mr. Gamini
        $gamini = Contact::where('company_id', $this->company->id)
            ->where('name', 'like', '%Gamini%')
            ->firstOrFail();

        $widgetGamini = Livewire::test(PaymentsOverviewWidget::class, [
            'tableFilters' => ['customer' => ['value' => $gamini->id]],
        ]);

        $filteredGamini = $widgetGamini->instance()->getViewData();
        $this->assertEquals(1900000.00, $filteredGamini['totalCollected'], 'Total collected must match Gamini payments');
        $this->assertEquals(3, $filteredGamini['transactionsCount']);
        $this->assertEquals(1900000.00, $filteredGamini['bankTransferTotal']);

        // Filter by Method: Cash (no cash payments were seeded, should be 0)
        $widgetCash = Livewire::test(PaymentsOverviewWidget::class, [
            'tableFilters' => ['method' => ['value' => 'cash']],
        ]);

        $filteredCash = $widgetCash->instance()->getViewData();
        $this->assertEquals(0.00, $filteredCash['totalCollected']);
        $this->assertEquals(0, $filteredCash['transactionsCount']);
    }

    public function test_payments_overview_widget_reacts_to_table_search(): void
    {
        // Search for "Fernando"
        $widgetFernando = Livewire::test(PaymentsOverviewWidget::class, [
            'tableSearch' => 'Fernando',
        ]);
        $searchData = $widgetFernando->instance()->getViewData();

        $this->assertEquals(1250000.00, $searchData['totalCollected']);
        $this->assertEquals(2, $searchData['transactionsCount']);

        // Search for "Gamini"
        $widgetGamini = Livewire::test(PaymentsOverviewWidget::class, [
            'tableSearch' => 'Gamini',
        ]);
        $searchGaminiData = $widgetGamini->instance()->getViewData();

        $this->assertEquals(1900000.00, $searchGaminiData['totalCollected']);
        $this->assertEquals(3, $searchGaminiData['transactionsCount']);
    }

    public function test_list_payments_page_renders_successfully(): void
    {
        Livewire::test(ListPayments::class)
            ->assertSuccessful();

        Livewire::test(PaymentsOverviewWidget::class)
            ->assertSuccessful()
            ->assertSee('Total Payments Collected')
            ->assertSee('Transactions Count')
            ->assertSee('Bank vs Cash Breakdown');
    }
}
