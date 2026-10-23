<?php

namespace App\Filament\App\Pages;

use App\Models\Contact;
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
            COALESCE(SUM(profit_markup), 0) as total_profit_markup,
            COALESCE(SUM(commission_amount), 0) as agent_commissions
        ')->first();

        $invoicedSales = round((float) ($invoiceSums?->invoiced_sales ?? 0.0), 2);
        $grossSales = round((float) ($invoiceSums?->gross_sales ?? 0.0), 2);
        $totalDiscounts = round((float) ($invoiceSums?->total_discounts ?? 0.0), 2);
        $totalTaxes = round((float) ($invoiceSums?->total_taxes ?? 0.0), 2);
        $totalProfitMarkup = round((float) ($invoiceSums?->total_profit_markup ?? 0.0), 2);
        $agentCommissions = round((float) ($invoiceSums?->agent_commissions ?? 0.0), 2);

        // Direct SQL Payment Aggregation
        $paymentsQuery = DB::table('payments')
            ->whereBetween('payment_date', [$startDate, $endDate]);
        if ($tenantId) {
            $paymentsQuery->where('company_id', $tenantId);
        }
        $paymentsCollected = round((float) $paymentsQuery->sum('amount'), 2);

        // Direct SQL Cost of Goods Sold (COGS) Aggregation (Profit Markup has 0 COGS)
        $cogsQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->leftJoin('products', 'invoice_items.product_id', '=', 'products.id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->where('invoices.status', '!=', 'cancelled');

        if ($tenantId) {
            $cogsQuery->where('invoices.company_id', $tenantId);
        }

        $cogs = round((float) $cogsQuery->sum(DB::raw('invoice_items.quantity * COALESCE(NULLIF(invoice_items.cost_price, 0), products.cost_price, 0)')), 2);

        // Direct SQL Operating Expenses Aggregation (Distinguishing external outflows vs internal overhead absorption)
        $expensesQuery = DB::table('expenses')
            ->whereBetween('expense_date', [$startDate, $endDate]);
        if ($tenantId) {
            $expensesQuery->where('company_id', $tenantId);
        }

        $expenseSums = (clone $expensesQuery)->selectRaw('
            COALESCE(SUM(CASE WHEN is_internal_overhead = 0 THEN amount ELSE 0 END), 0) as external_expenses,
            COALESCE(SUM(CASE WHEN is_internal_overhead = 1 THEN amount ELSE 0 END), 0) as internal_overhead,
            COALESCE(SUM(amount), 0) as total_expenses
        ')->first();

        $externalExpenses = round((float) ($expenseSums?->external_expenses ?? 0.0), 2);
        $internalOverhead = round((float) ($expenseSums?->internal_overhead ?? 0.0), 2);
        $totalExpenses = round((float) ($expenseSums?->total_expenses ?? 0.0), 2);
        // Only external vendor outflows are deducted from bottom-line retained earnings
        $operatingExpenses = $externalExpenses;

        // Derived Profit & Loss: Pure profit markup adds 100% to gross & net profit
        $grossProfit = round($invoicedSales - $cogs, 2);
        $grossMarginPercentage = $invoicedSales > 0 ? round(($grossProfit / $invoicedSales) * 100, 1) : 0.0;
        // Internal company overhead is NOT deducted from tenant bottom-line retained earnings
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
        $receivablesSummary = $receivablesQuery->get()->toArray();

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

        $expenseBreakdown = $rawExpenses->map(function ($item) use ($totalExpenses) {
            $total = (float) $item->total;
            $count = (int) $item->count;
            $percentage = $totalExpenses > 0 ? round(($total / $totalExpenses) * 100, 1) : 0.0;

            return [
                'category' => $item->category,
                'count' => $count,
                'total' => $total,
                'percentage' => $percentage,
            ];
        })->toArray();

        return [
            'invoiced_sales' => $invoicedSales,
            'gross_sales' => $grossSales,
            'total_discounts' => $totalDiscounts,
            'total_taxes' => $totalTaxes,
            'total_profit_markup' => $totalProfitMarkup,
            'payments_collected' => $paymentsCollected,
            'cogs' => $cogs,
            'operating_expenses' => $operatingExpenses,
            'external_expenses' => $externalExpenses,
            'internal_overhead' => $internalOverhead,
            'total_expenses' => $totalExpenses,
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

    public function getExternalExpenses(): float
    {
        return (float) ($this->getFinancialMetrics()['external_expenses'] ?? 0.0);
    }

    public function getInternalOverhead(): float
    {
        return (float) ($this->getFinancialMetrics()['internal_overhead'] ?? 0.0);
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
        $data = $this->getFinancialMetrics()['receivables_summary'] ?? [];
        if ($data instanceof Collection) {
            return $data;
        }

        $items = is_array($data) ? $data : [];

        return collect($items)->map(function ($item) {
            if ($item instanceof Invoice) {
                return $item;
            }

            if (is_array($item)) {
                $contactData = $item['contact'] ?? null;
                unset($item['contact']);
                $invoice = (new Invoice)->newFromBuilder($item);
                if (is_array($contactData)) {
                    $invoice->setRelation('contact', (new Contact)->newFromBuilder($contactData));
                }

                return $invoice;
            }

            return $item;
        });
    }

    public function getTotalReceivables(): float
    {
        return (float) ($this->getFinancialMetrics()['total_receivables'] ?? 0.0);
    }

    public function getExpenseBreakdown(): Collection
    {
        $data = $this->getFinancialMetrics()['expense_breakdown'] ?? [];
        if ($data instanceof Collection) {
            return $data;
        }

        $items = is_array($data) ? $data : [];

        return collect($items)->map(function ($item) {
            return is_array($item) ? (object) $item : $item;
        });
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
            'expenses:id,invoice_id,title,category,amount,expense_date,is_internal_overhead',
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

        // Distinguish External Third-Party Expenses vs Internal Company Overhead Allocation
        $externalExpenses = $linkedExpenses->where('is_internal_overhead', false);
        $internalExpenses = $linkedExpenses->where('is_internal_overhead', true);

        // Section A: Direct External Vendor Outflow
        // 1. Direct Materials & Hardware (Materials Bills)
        $materialsCost = (float) $externalExpenses
            ->filter(fn ($e) => preg_match('/material|hardware|equipment|tool|cable|component|solar|panel|inverter/i', (string) $e->category))
            ->sum('amount');

        // Fallback to item cost prices if no explicit external material expense voucher is logged yet
        if ($materialsCost <= 0 && (float) $invoice->total_cost > 0) {
            $materialsCost = (float) $invoice->total_cost;
        }

        // 2. Installation Labour & Site Logistics
        $labourCost = (float) $externalExpenses
            ->filter(fn ($e) => preg_match('/labour|labor|installation|contractor|technician|travel|food|transport|logistics|site/i', (string) $e->category))
            ->sum('amount');

        $otherExternalCost = (float) $externalExpenses
            ->filter(fn ($e) => ! preg_match('/material|hardware|equipment|tool|cable|component|solar|panel|inverter|labour|labor|installation|contractor|technician|travel|food|transport|logistics|site/i', (string) $e->category))
            ->sum('amount');

        $labourAndOps = $labourCost + $otherExternalCost;

        // 3. Sales Agent Commission
        $commissionAmount = (float) ($invoice->commission_amount ?? 0);

        // Section A Total: Direct External Vendor Outflow
        $directExternalOutflow = round($materialsCost + $labourAndOps + $commissionAmount, 2);

        // Section B: Internal Company Allocation (Office Overhead, e.g. Rs. 112,000.00)
        $internalCompanyOverhead = round((float) $internalExpenses->sum('amount'), 2);

        // Company Retained Margin = Contract Revenue - Direct External Outflow (Keeps overhead inside gross profit pool!)
        $companyRetainedMargin = round($contractValue - $directExternalOutflow, 2);
        $retainedMarginPercentage = $contractValue > 0 ? round(($companyRetainedMargin / $contractValue) * 100, 1) : 0.0;

        // Total Combined Cost and Net Pure Profit after internal allocation
        $totalProjectCost = round($directExternalOutflow + $internalCompanyOverhead, 2);
        $netProfit = round($contractValue - $totalProjectCost, 2);
        $netMargin = $contractValue > 0 ? round(($netProfit / $contractValue) * 100, 1) : 0.0;
        $isProfitable = $netProfit >= 0;

        // Share breakdown for ApexCharts
        $chartBasis = max($contractValue, $totalProjectCost);
        $materialPct = $chartBasis > 0 ? round(($materialsCost / $chartBasis) * 100, 1) : 0.0;
        $labourPct = $chartBasis > 0 ? round(($labourAndOps / $chartBasis) * 100, 1) : 0.0;
        $commPct = $chartBasis > 0 ? round(($commissionAmount / $chartBasis) * 100, 1) : 0.0;
        $overheadPct = $chartBasis > 0 ? round(($internalCompanyOverhead / $chartBasis) * 100, 1) : 0.0;
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
            'direct_external_outflow' => $directExternalOutflow,
            'internal_company_overhead' => $internalCompanyOverhead,
            'company_retained_margin' => $companyRetainedMargin,
            'retained_margin_percentage' => $retainedMarginPercentage,
            'total_project_cost' => $totalProjectCost,
            'net_profit' => $netProfit,
            'net_margin' => $netMargin,
            'is_profitable' => $isProfitable,
            'linked_expenses' => $linkedExpenses,
            'external_expenses' => $externalExpenses,
            'internal_expenses' => $internalExpenses,
            'chart_series' => [$materialPct, $labourPct, $commPct, $overheadPct, $profitPct],
            'chart_amounts' => [$materialsCost, $labourAndOps, $commissionAmount, $internalCompanyOverhead, max(0, $netProfit)],
            'chart_labels' => ['Material Bills', 'Installation Labour', 'Agent Commission', 'Internal Company Overhead', 'Pure Net Margin'],
            'chart_colors' => ['#0284c7', '#f59e0b', '#8b5cf6', '#d97706', '#10b981'],
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
