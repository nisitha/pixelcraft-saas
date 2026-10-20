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
use Illuminate\Support\Facades\DB;

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
        $this->selectedInvoiceId = Invoice::where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->latest('id')
            ->value('id');
    }

    public function selectInvoice(int $invoiceId): void
    {
        $this->selectedInvoiceId = $invoiceId;
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;

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

    public function getInvoicedSales(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Invoice::where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$this->startDate, $this->endDate])
            ->sum('grand_total');
    }

    public function getGrossSales(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Invoice::where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$this->startDate, $this->endDate])
            ->sum('subtotal');
    }

    public function getTotalDiscounts(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Invoice::where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$this->startDate, $this->endDate])
            ->sum('discount');
    }

    public function getTotalTaxes(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Invoice::where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$this->startDate, $this->endDate])
            ->sum('tax');
    }

    public function getPaymentsCollected(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Payment::where('company_id', $tenantId)
            ->whereBetween('payment_date', [$this->startDate, $this->endDate])
            ->sum('amount');
    }

    public function getCostOfGoodsSold(): float
    {
        $tenantId = Filament::getTenant()?->id;

        $invoices = Invoice::with(['items.product'])
            ->where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$this->startDate, $this->endDate])
            ->get();

        $cogs = 0.0;
        foreach ($invoices as $inv) {
            foreach ($inv->items as $item) {
                $unitCost = (float) ($item->product?->cost_price ?? 0);
                $cogs += ((float) $item->quantity * $unitCost);
            }
        }

        return round($cogs, 2);
    }

    public function getOperatingExpenses(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Expense::where('company_id', $tenantId)
            ->whereBetween('expense_date', [$this->startDate, $this->endDate])
            ->sum('amount');
    }

    public function getAgentCommissions(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Invoice::where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('invoice_date', [$this->startDate, $this->endDate])
            ->sum('commission_amount');
    }

    public function getGrossProfit(): float
    {
        return round($this->getInvoicedSales() - $this->getCostOfGoodsSold(), 2);
    }

    public function getGrossMarginPercentage(): float
    {
        $sales = $this->getInvoicedSales();
        if ($sales <= 0) {
            return 0.0;
        }

        return round(($this->getGrossProfit() / $sales) * 100, 1);
    }

    public function getNetProfit(): float
    {
        return round($this->getGrossProfit() - $this->getOperatingExpenses() - $this->getAgentCommissions(), 2);
    }

    public function getNetMarginPercentage(): float
    {
        $sales = $this->getInvoicedSales();
        if ($sales <= 0) {
            return 0.0;
        }

        return round(($this->getNetProfit() / $sales) * 100, 1);
    }

    public function getReceivablesSummary(): Collection
    {
        $tenantId = Filament::getTenant()?->id;

        return Invoice::with('contact')
            ->where('company_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_amount', '>', 0)
            ->orderByDesc('due_amount')
            ->get();
    }

    public function getTotalReceivables(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Invoice::where('company_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('due_amount');
    }

    public function getExpenseBreakdown(): Collection
    {
        $tenantId = Filament::getTenant()?->id;

        $expenses = Expense::where('company_id', $tenantId)
            ->whereBetween('expense_date', [$this->startDate, $this->endDate])
            ->select('category', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $totalExp = $this->getOperatingExpenses();

        return $expenses->map(function ($item) use ($totalExp) {
            $item->percentage = $totalExp > 0 ? round(($item->total / $totalExp) * 100, 1) : 0;

            return $item;
        });
    }

    public function getRealizedCashCollected(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Payment::where('company_id', $tenantId)
            ->whereBetween('payment_date', [$this->startDate, $this->endDate])
            ->sum('amount');
    }

    public function getPendingCashToCollect(): float
    {
        $tenantId = Filament::getTenant()?->id;

        return (float) Invoice::where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->where('due_amount', '>', 0)
            ->sum('due_amount');
    }

    public function getCollectionRatePercentage(): float
    {
        $realized = $this->getRealizedCashCollected();
        $pending = $this->getPendingCashToCollect();
        $total = $realized + $pending;

        return $total > 0 ? round(($realized / $total) * 100, 1) : 100.0;
    }

    public function getInvoicesList(): Collection
    {
        $tenantId = Filament::getTenant()?->id;

        return Invoice::with(['contact', 'salesAgent'])
            ->where('company_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('id')
            ->limit(100)
            ->get();
    }

    public function getSelectedInvoice(): ?Invoice
    {
        $tenantId = Filament::getTenant()?->id;

        if (! $this->selectedInvoiceId) {
            $this->selectedInvoiceId = Invoice::where('company_id', $tenantId)
                ->where('status', '!=', 'cancelled')
                ->latest('id')
                ->value('id');
        }

        if (! $this->selectedInvoiceId) {
            return null;
        }

        return Invoice::with(['contact', 'items.product', 'payments', 'expenses', 'salesAgent'])
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
}
