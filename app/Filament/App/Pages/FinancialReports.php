<?php

namespace App\Filament\App\Pages;

use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use BackedEnum;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinancialReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Financial Reports';

    protected static ?string $title = 'Accounting & P&L Financial Reports';

    protected static ?string $slug = 'financial-reports';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.app.pages.financial-reports';

    public string $period = 'all';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?int $selectedInvoiceId = null;

    /**
     * In-memory memoized metrics for the current request cycle.
     */
    protected ?array $metricsCache = null;

    /**
     * Revision timestamp per tenant to bust in-memory instance caches when observers flush.
     */
    protected static array $tenantRevisions = [];

    protected ?float $instanceRevision = null;

    public function clearMetricsCache(): void
    {
        $this->metricsCache = null;
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        return $user->isTenantOwnerOrAdmin();
    }

    public function mount(): void
    {
        $this->setPeriod('all');

        $tenantId = Filament::getTenant()?->id;
        if ($tenantId) {
            $this->selectedInvoiceId = (int) (Invoice::where('company_id', $tenantId)
                ->where('status', '!=', 'cancelled')
                ->latest('id')
                ->value('id') ?? 0);
        }
    }

    public function selectInvoice(int $invoiceId): void
    {
        $this->selectedInvoiceId = $invoiceId;
    }

    public function updatedStartDate(): void
    {
        $this->metricsCache = null;
    }

    public function updatedEndDate(): void
    {
        $this->metricsCache = null;
    }

    public function updatedPeriod(): void
    {
        $this->metricsCache = null;
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->metricsCache = null;

        switch ($period) {
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->toDateString();
                $this->endDate = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->startDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                $this->endDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'this_quarter':
                $this->startDate = Carbon::now()->startOfQuarter()->toDateString();
                $this->endDate = Carbon::now()->endOfQuarter()->toDateString();
                break;
            case 'this_year':
                $this->startDate = Carbon::now()->startOfYear()->toDateString();
                $this->endDate = Carbon::now()->endOfYear()->toDateString();
                break;
            case 'all':
            default:
                $this->startDate = '2020-01-01';
                $this->endDate = Carbon::now()->addYear()->toDateString();
                break;
        }
    }

    /**
     * Generate the standardized cache key for tenant financial reports.
     */
    public static function getCacheKey(int|string $tenantId, string $range, string $startDate, string $endDate): string
    {
        return "tenant_{$tenantId}_fin_reports_{$range}_{$startDate}_{$endDate}";
    }

    /**
     * Invalidate and flush all financial report cached metrics for the tenant.
     */
    public static function flushCacheForTenant(int|string|null $tenantId): void
    {
        if (! $tenantId) {
            return;
        }

        static::$tenantRevisions[$tenantId] = microtime(true);

        $trackedKeys = Cache::get("tenant_{$tenantId}_fin_reports_keys", []);
        if (is_array($trackedKeys)) {
            foreach ($trackedKeys as $key) {
                Cache::forget($key);
            }
        }
        Cache::forget("tenant_{$tenantId}_fin_reports_keys");

        try {
            if (Schema::hasTable('cache')) {
                DB::table('cache')
                    ->where('key', 'like', "%tenant_{$tenantId}_fin_reports_%")
                    ->delete();
            }
        } catch (\Throwable $e) {
            // Silently continue if database cache driver is not configured or accessible
        }
    }

    /**
     * Retrieve the cached aggregated financial metrics or compute them via direct SQL aggregations.
     *
     * @return array<string, mixed>
     */
    public function getFinancialMetrics(): array
    {
        $tenantId = Filament::getTenant()?->id;
        $currentRevision = $tenantId ? (static::$tenantRevisions[$tenantId] ?? 0.0) : 0.0;

        if ($this->metricsCache !== null && $this->instanceRevision === $currentRevision) {
            return $this->metricsCache;
        }

        $range = $this->period ?? 'all';
        $startDate = $this->startDate ?? '2020-01-01';
        $endDate = $this->endDate ?? Carbon::now()->addYear()->toDateString();

        if (! $tenantId) {
            return $this->calculateMetrics(null, $startDate, $endDate);
        }

        $cacheKey = static::getCacheKey($tenantId, $range, $startDate, $endDate);

        $metrics = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($tenantId, $startDate, $endDate) {
            return $this->calculateMetrics($tenantId, $startDate, $endDate);
        });

        // Track cache key for tenant so observers can flush it
        $trackedKeys = Cache::get("tenant_{$tenantId}_fin_reports_keys", []);
        if (! is_array($trackedKeys)) {
            $trackedKeys = [];
        }
        if (! in_array($cacheKey, $trackedKeys, true)) {
            $trackedKeys[] = $cacheKey;
            Cache::put("tenant_{$tenantId}_fin_reports_keys", $trackedKeys, now()->addMinutes(60));
        }

        $this->metricsCache = $metrics;
        $this->instanceRevision = $currentRevision;

        return $metrics;
    }

    /**
     * Compute all metrics via single-query direct SQL aggregations without in-memory Eloquent looping.
     *
     * @return array<string, mixed>
     */
    protected function calculateMetrics(?int $tenantId, string $startDate, string $endDate): array
    {
        $invoiceQuery = DB::table('invoices')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled');

        if ($tenantId) {
            $invoiceQuery->where('company_id', $tenantId);
        }

        $invoiceSums = $invoiceQuery->selectRaw('
            COALESCE(SUM(grand_total), 0) as invoiced_sales,
            COALESCE(SUM(subtotal), 0) as gross_sales,
            COALESCE(SUM(discount), 0) as total_discounts,
            COALESCE(SUM(tax), 0) as total_taxes,
            COALESCE(SUM(commission_amount), 0) as agent_commissions
        ')->first();

        $invoicedSales = round((float) ($invoiceSums?->invoiced_sales ?? 0.0), 2);
        $grossSales = round((float) ($invoiceSums?->gross_sales ?? 0.0), 2);
        $totalDiscounts = round((float) ($invoiceSums?->total_discounts ?? 0.0), 2);
        $totalTaxes = round((float) ($invoiceSums?->total_taxes ?? 0.0), 2);
        $agentCommissions = round((float) ($invoiceSums?->agent_commissions ?? 0.0), 2);

        // Direct SQL Payment Aggregation
        $paymentsQuery = DB::table('payments')
            ->whereBetween('payment_date', [$startDate, $endDate]);
        if ($tenantId) {
            $paymentsQuery->where('company_id', $tenantId);
        }
        $paymentsCollected = round((float) $paymentsQuery->sum('amount'), 2);

        // Direct SQL Cost of Goods Sold (COGS) Aggregation
        $cogsQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftJoin('products', 'invoice_items.product_id', '=', 'products.id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', '!=', 'cancelled');

        if ($tenantId) {
            $cogsQuery->where('invoices.company_id', $tenantId);
        }

        $cogs = round((float) $cogsQuery->sum(DB::raw('invoice_items.quantity * COALESCE(NULLIF(invoice_items.cost_price, 0), products.cost_price, 0)')), 2);

        // Direct SQL Operating Expenses Aggregation
        $expensesQuery = DB::table('expenses')
            ->whereBetween('expense_date', [$startDate, $endDate]);
        if ($tenantId) {
            $expensesQuery->where('company_id', $tenantId);
        }
        $operatingExpenses = round((float) $expensesQuery->sum('amount'), 2);

        // Derived Profit & Loss
        $grossProfit = round($invoicedSales - $cogs, 2);
        $grossMarginPercentage = $invoicedSales > 0 ? round(($grossProfit / $invoicedSales) * 100, 1) : 0.0;
        $netProfit = round($grossProfit - $operatingExpenses - $agentCommissions, 2);
        $netMarginPercentage = $invoicedSales > 0 ? round(($netProfit / $invoicedSales) * 100, 1) : 0.0;

        // Pending Cash to Collect & Total Receivables
        $pendingCashQuery = DB::table('invoices')
            ->where('status', '!=', 'cancelled')
            ->where('due_amount', '>', 0);
        if ($tenantId) {
            $pendingCashQuery->where('company_id', $tenantId);
        }
        $pendingCashToCollect = round((float) $pendingCashQuery->sum('due_amount'), 2);

        $totalReceivablesQuery = DB::table('invoices')
            ->whereIn('status', ['unpaid', 'partial']);
        if ($tenantId) {
            $totalReceivablesQuery->where('company_id', $tenantId);
        }
        $totalReceivables = round((float) $totalReceivablesQuery->sum('due_amount'), 2);

        $totalCash = $paymentsCollected + $pendingCashToCollect;
        $collectionRatePercentage = $totalCash > 0 ? round(($paymentsCollected / $totalCash) * 100, 1) : 100.0;

        // Receivables Aging Summary (limit 50, selecting only needed columns)
        $receivablesQuery = Invoice::select([
            'id', 'invoice_number', 'contact_id', 'invoice_date',
            'grand_total', 'paid_amount', 'due_amount', 'status',
        ])
            ->with(['contact:id,name,phone,ceb_account_no'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_amount', '>', 0)
            ->orderByDesc('due_amount')
            ->limit(50);

        if ($tenantId) {
            $receivablesQuery->where('company_id', $tenantId);
        }
        $receivablesSummary = $receivablesQuery->get();

        // Expense Breakdown (Grouped in SQL)
        $expGroupQuery = DB::table('expenses')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->select('category', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total');

        if ($tenantId) {
            $expGroupQuery->where('company_id', $tenantId);
        }
        $rawExpenses = $expGroupQuery->get();

        $expenseBreakdown = $rawExpenses->map(function ($item) use ($operatingExpenses) {
            $total = (float) $item->total;
            $count = (int) $item->count;
            $percentage = $operatingExpenses > 0 ? round(($total / $operatingExpenses) * 100, 1) : 0.0;

            return (object) [
                'category' => $item->category,
                'count' => $count,
                'total' => $total,
                'percentage' => $percentage,
            ];
        });

        return [
            'invoiced_sales' => $invoicedSales,
            'gross_sales' => $grossSales,
            'total_discounts' => $totalDiscounts,
            'total_taxes' => $totalTaxes,
            'payments_collected' => $paymentsCollected,
            'cogs' => $cogs,
            'operating_expenses' => $operatingExpenses,
            'agent_commissions' => $agentCommissions,
            'gross_profit' => $grossProfit,
            'gross_margin_percentage' => $grossMarginPercentage,
            'net_profit' => $netProfit,
            'net_margin_percentage' => $netMarginPercentage,
            'realized_cash_collected' => $paymentsCollected,
            'pending_cash_to_collect' => $pendingCashToCollect,
            'collection_rate_percentage' => $collectionRatePercentage,
            'total_receivables' => $totalReceivables,
            'expense_breakdown' => $expenseBreakdown,
            'receivables_summary' => $receivablesSummary,
        ];
    }

    public function getInvoicedSales(): float
    {
        return (float) ($this->getFinancialMetrics()['invoiced_sales'] ?? 0.0);
    }

    public function getGrossSales(): float
    {
        return (float) ($this->getFinancialMetrics()['gross_sales'] ?? 0.0);
    }

    public function getTotalDiscounts(): float
    {
        return (float) ($this->getFinancialMetrics()['total_discounts'] ?? 0.0);
    }

    public function getTotalTaxes(): float
    {
        return (float) ($this->getFinancialMetrics()['total_taxes'] ?? 0.0);
    }

    public function getPaymentsCollected(): float
    {
        return (float) ($this->getFinancialMetrics()['payments_collected'] ?? 0.0);
    }

    public function getCostOfGoodsSold(): float
    {
        return (float) ($this->getFinancialMetrics()['cogs'] ?? 0.0);
    }

    public function getOperatingExpenses(): float
    {
        return (float) ($this->getFinancialMetrics()['operating_expenses'] ?? 0.0);
    }

    public function getAgentCommissions(): float
    {
        return (float) ($this->getFinancialMetrics()['agent_commissions'] ?? 0.0);
    }

    public function getGrossProfit(): float
    {
        return (float) ($this->getFinancialMetrics()['gross_profit'] ?? 0.0);
    }

    public function getGrossMarginPercentage(): float
    {
        return (float) ($this->getFinancialMetrics()['gross_margin_percentage'] ?? 0.0);
    }

    public function getNetProfit(): float
    {
        return (float) ($this->getFinancialMetrics()['net_profit'] ?? 0.0);
    }

    public function getNetMarginPercentage(): float
    {
        return (float) ($this->getFinancialMetrics()['net_margin_percentage'] ?? 0.0);
    }

    public function getReceivablesSummary(): Collection
    {
        return $this->getFinancialMetrics()['receivables_summary'] ?? collect();
    }

    public function getTotalReceivables(): float
    {
        return (float) ($this->getFinancialMetrics()['total_receivables'] ?? 0.0);
    }

    public function getExpenseBreakdown(): Collection
    {
        return $this->getFinancialMetrics()['expense_breakdown'] ?? collect();
    }

    public function getRealizedCashCollected(): float
    {
        return (float) ($this->getFinancialMetrics()['realized_cash_collected'] ?? 0.0);
    }

    public function getPendingCashToCollect(): float
    {
        return (float) ($this->getFinancialMetrics()['pending_cash_to_collect'] ?? 0.0);
    }

    public function getCollectionRatePercentage(): float
    {
        return (float) ($this->getFinancialMetrics()['collection_rate_percentage'] ?? 100.0);
    }

    public function getInvoicesList(): Collection
    {
        $tenantId = Filament::getTenant()?->id;
        if (! $tenantId) {
            return collect();
        }

        return Invoice::select(['id', 'invoice_number', 'grand_total', 'contact_id'])
            ->with('contact:id,name')
            ->where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('id')
            ->limit(100)
            ->get();
    }

    public function getSelectedInvoice(): ?Invoice
    {
        $tenantId = Filament::getTenant()?->id;
        if (! $tenantId) {
            return null;
        }

        if (! $this->selectedInvoiceId) {
            $this->selectedInvoiceId = Invoice::where('company_id', $tenantId)
                ->where('status', '!=', 'cancelled')
                ->latest('id')
                ->value('id');
        }

        if (! $this->selectedInvoiceId) {
            return null;
        }

        return Invoice::with([
            'contact:id,name,ceb_account_no',
            'salesAgent:id,name',
            'expenses:id,invoice_id,title,category,amount,expense_date',
            'items:id,invoice_id,cost_price,quantity',
        ])
            ->where('company_id', $tenantId)
            ->find($this->selectedInvoiceId);
    }

    /**
     * @return array<string, mixed>
     */
    public function getInvoiceProfitabilityMetrics(): array
    {
        $invoice = $this->getSelectedInvoice();

        if (! $invoice) {
            return [
                'has_data' => false,
            ];
        }

        $contractValue = (float) ($invoice->grand_total ?? 0);
        $collectedAmount = (float) ($invoice->paid_amount ?? 0);
        $balanceDue = (float) ($invoice->due_amount ?? 0);

        $linkedExpenses = $invoice->expenses ?? collect();

        // Direct Materials & Hardware
        $materialsCost = (float) $linkedExpenses
            ->filter(fn ($e) => preg_match('/material|hardware|equipment|tool|cable|component|solar|panel|inverter/i', (string) $e->category))
            ->sum('amount');

        // Fallback to item cost prices if no explicit material expense is logged yet
        if ($materialsCost <= 0 && (float) $invoice->total_cost > 0) {
            $materialsCost = (float) $invoice->total_cost;
        }

        // Labour, food, transport & operational overheads
        $labourCost = (float) $linkedExpenses
            ->filter(fn ($e) => preg_match('/labour|labor|installation|contractor|technician|travel|food|transport|logistics|site|overhead|utility|utilities/i', (string) $e->category))
            ->sum('amount');

        $otherCost = (float) $linkedExpenses
            ->filter(fn ($e) => ! preg_match('/material|hardware|equipment|tool|cable|component|solar|panel|inverter|labour|labor|installation|contractor|technician|travel|food|transport|logistics|site|overhead|utility|utilities/i', (string) $e->category))
            ->sum('amount');

        $labourAndOps = $labourCost + $otherCost;

        // Sales Agent Commission
        $commissionAmount = (float) ($invoice->commission_amount ?? 0);

        // Total Project Cost
        $totalProjectCost = round($materialsCost + $labourAndOps + $commissionAmount, 2);

        // Net Profit / Loss
        $netProfit = round($contractValue - $totalProjectCost, 2);
        $netMargin = $contractValue > 0 ? round(($netProfit / $contractValue) * 100, 1) : 0.0;
        $isProfitable = $netProfit >= 0;

        // Share breakdown for ApexCharts
        $chartBasis = max($contractValue, $totalProjectCost);
        $materialPct = $chartBasis > 0 ? round(($materialsCost / $chartBasis) * 100, 1) : 0.0;
        $labourPct = $chartBasis > 0 ? round(($labourAndOps / $chartBasis) * 100, 1) : 0.0;
        $commPct = $chartBasis > 0 ? round(($commissionAmount / $chartBasis) * 100, 1) : 0.0;
        $profitPct = ($chartBasis > 0 && $netProfit > 0) ? round(($netProfit / $chartBasis) * 100, 1) : 0.0;

        return [
            'has_data' => true,
            'invoice' => $invoice,
            'contract_value' => $contractValue,
            'collected_amount' => $collectedAmount,
            'balance_due' => $balanceDue,
            'collection_percentage' => $contractValue > 0 ? round(($collectedAmount / $contractValue) * 100, 1) : 0.0,
            'materials_cost' => $materialsCost,
            'labour_operational_cost' => $labourAndOps,
            'commission_amount' => $commissionAmount,
            'total_project_cost' => $totalProjectCost,
            'net_profit' => $netProfit,
            'net_margin' => $netMargin,
            'is_profitable' => $isProfitable,
            'linked_expenses' => $linkedExpenses,
            'chart_series' => [$materialPct, $labourPct, $commPct, $profitPct],
            'chart_amounts' => [$materialsCost, $labourAndOps, $commissionAmount, max(0, $netProfit)],
            'chart_labels' => ['Direct Materials & Bills', 'Labour & Operations', 'Agent Commission', 'Net Profit Margin'],
            'chart_colors' => ['#0284c7', '#f59e0b', '#8b5cf6', '#10b981'],
        ];
    }

    /**
     * Pass computed metrics and data dynamically to view to prevent Livewire from re-serializing datasets.
     *
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'metrics' => $this->getFinancialMetrics(),
            'pnl' => $this->getInvoiceProfitabilityMetrics(),
            'availableInvoices' => $this->getInvoicesList(),
        ];
    }
}
