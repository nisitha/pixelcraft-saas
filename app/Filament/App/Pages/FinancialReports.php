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

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        return ! $user->isTenantCashier();
    }

    public function mount(): void
    {
        $this->setPeriod('all');
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
        return round($this->getGrossProfit() - $this->getOperatingExpenses(), 2);
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
}
