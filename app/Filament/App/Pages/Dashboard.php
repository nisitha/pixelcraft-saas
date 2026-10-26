<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Resources\Quotations\QuotationResource;
use App\Models\CompletionCertificate;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Quotation;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected string $view = 'filament.app.pages.able-pro-dashboard';

    public function getWidgets(): array
    {
        return [];
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    protected function getViewData(): array
    {
        $tenant = Filament::getTenant();
        $user = Filament::auth()->user();
        $isStaff = ! ($user?->isTenantOwnerOrAdmin($tenant) ?? false);

        // ========================================================
        // 1. STAFF WORKSPACE LAUNCHPAD (Non-Owner / Non-Admin)
        // ========================================================
        if ($isStaff) {
            $rawRole = $user?->roleInTenant($tenant) ?? 'staff';
            $staffRoleTitle = match ($rawRole) {
                'sales_executive' => 'Sales Executive',
                'cashier' => 'Cashier / Counter POS',
                'engineer' => 'Site & Installation Engineer',
                default => 'Staff Member',
            };

            $todayQuotesCount = Quotation::where('company_id', $tenant?->id)
                ->whereDate('created_at', today())
                ->count();

            $todayInvoicesCount = Invoice::where('company_id', $tenant?->id)
                ->whereDate('created_at', today())
                ->count();

            $todayCertificatesCount = CompletionCertificate::where('company_id', $tenant?->id)
                ->whereDate('created_at', today())
                ->count();

            $todayPaymentsCount = Payment::where('company_id', $tenant?->id)
                ->whereDate('created_at', today())
                ->count();

            // Recent activity for staff (no financial profit/cost amounts)
            $staffRecentDocs = Quotation::where('company_id', $tenant?->id)
                ->with('contact')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($q) => [
                    'type' => 'Quotation',
                    'ref' => $q->quote_number,
                    'customer' => $q->contact?->name ?? 'Direct Customer',
                    'date' => $q->quote_date?->format('M d, Y') ?? 'Recent',
                    'status' => $q->status,
                    'url' => QuotationResource::getUrl('view', ['record' => $q->id]),
                ]);

            return [
                'tenant' => $tenant,
                'company' => $tenant,
                'user' => $user,
                'isStaff' => true,
                'staffRole' => $rawRole,
                'staffRoleTitle' => $staffRoleTitle,
                'todayQuotesCount' => $todayQuotesCount,
                'todayInvoicesCount' => $todayInvoicesCount,
                'todayCertificatesCount' => $todayCertificatesCount,
                'todayPaymentsCount' => $todayPaymentsCount,
                'staffRecentDocs' => $staffRecentDocs,
            ];
        }

        // ========================================================
        // 2. OWNER & ADMIN ABLE PRO EXECUTIVE DASHBOARD
        // ========================================================
        // 1. All Earnings (Total Invoiced Revenue)
        $totalRevenue = (float) Invoice::where('company_id', $tenant?->id)
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');

        // 2. Quotations Count
        $quotationsCount = Quotation::where('company_id', $tenant?->id)->count();

        // 3. Total Invoices / Projects Count
        $invoicesCount = Invoice::where('company_id', $tenant?->id)->count();

        // 4. Total Expenses (COGS + Commissions + Standalone Expenses)
        $invoices = Invoice::where('company_id', $tenant?->id)
            ->where('status', '!=', 'cancelled')
            ->with('items')
            ->get();

        $totalCogs = (float) $invoices->sum(fn ($inv) => $inv->total_cost);
        $commissions = (float) $invoices->sum('commission_amount');
        $standaloneExpenses = (float) Expense::where('company_id', $tenant?->id)->sum('amount');

        $totalExpenses = $totalCogs + $commissions + $standaloneExpenses;

        // 5. Net Profit & Margin
        $netProfit = round($totalRevenue - $totalExpenses, 2);
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0.0;

        $materialCost = round($totalCogs, 2);
        $overheadAndCommissions = round($commissions + $standaloneExpenses, 2);

        // 6. Pipeline Counts
        $pendingQuotationsCount = Quotation::where('company_id', $tenant?->id)
            ->whereIn('status', ['draft', 'sent'])
            ->count();

        $activeInstallationsCount = Invoice::where('company_id', $tenant?->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->count();

        $commissionedSystemsCount = Invoice::where('company_id', $tenant?->id)
            ->where('status', 'paid')
            ->count();

        $certificatesCount = CompletionCertificate::where('company_id', $tenant?->id)->count();

        // 7. Recent Transactions (Real Payments & Standalone Expenses)
        $payments = Payment::where('company_id', $tenant?->id)
            ->with('invoice.contact')
            ->latest('payment_date')
            ->take(6)
            ->get()
            ->map(function ($p) {
                $name = $p->invoice?->contact?->name ?? 'Customer Payment';

                return [
                    'type' => 'income',
                    'title' => $name,
                    'subtitle' => $p->reference_no ?: ('#'.($p->invoice?->invoice_number ?? 'REC-'.$p->id)),
                    'amount' => (float) $p->amount,
                    'date' => $p->payment_date?->format('M d, Y') ?? 'Recent',
                    'initials' => Str::upper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 2)) ?: 'CP',
                ];
            });

        $expenses = Expense::where('company_id', $tenant?->id)
            ->latest('expense_date')
            ->take(6)
            ->get()
            ->map(function ($e) {
                return [
                    'type' => 'expense',
                    'title' => $e->title ?: ($e->category ?? 'Expense'),
                    'subtitle' => $e->reference_no ? ('#'.$e->reference_no) : ('#'.$e->category),
                    'amount' => (float) $e->amount,
                    'date' => $e->expense_date?->format('M d, Y') ?? 'Recent',
                    'initials' => Str::upper(substr(preg_replace('/[^A-Za-z]/', '', $e->title ?: 'EX'), 0, 2)) ?: 'EX',
                ];
            });

        $allTransactions = $payments->concat($expenses)
            ->sortByDesc('date')
            ->values()
            ->take(6);

        // 8. Team members
        $teamMembers = $tenant?->users()->take(4)->get() ?? collect();

        // 9. Monthly Data Series for Charts (Jan - Dec Current Year)
        $monthlyRevenue = array_fill(0, 12, 0.0);
        $monthlyExpenses = array_fill(0, 12, 0.0);
        $monthlyCashflow = array_fill(0, 12, 0.0);

        $currentYear = (int) now()->year;
        $currentYearInvoices = $invoices->filter(function ($inv) use ($currentYear) {
            return $inv->invoice_date && (int) $inv->invoice_date->format('Y') === $currentYear;
        });

        foreach ($currentYearInvoices as $inv) {
            if ($inv->invoice_date) {
                $m = (int) $inv->invoice_date->format('n') - 1;
                $monthlyRevenue[$m] += (float) ($inv->grand_total ?? 0);
                $cost = (float) ($inv->total_cost ?? 0);
                $commission = (float) ($inv->commission_amount ?? 0);
                $monthlyExpenses[$m] += ($cost + $commission);
            }
        }

        $currentYearExpenses = Expense::where('company_id', $tenant?->id)
            ->whereYear('expense_date', now()->year)
            ->get(['expense_date', 'amount']);

        foreach ($currentYearExpenses as $exp) {
            if ($exp->expense_date) {
                $m = (int) $exp->expense_date->format('n') - 1;
                $monthlyExpenses[$m] += (float) $exp->amount;
            }
        }

        for ($i = 0; $i < 12; $i++) {
            $monthlyCashflow[$i] = round($monthlyRevenue[$i] - $monthlyExpenses[$i], 2);
            $monthlyRevenue[$i] = round($monthlyRevenue[$i], 2);
            $monthlyExpenses[$i] = round($monthlyExpenses[$i], 2);
        }

        // 10. Category Performance for Radial Bar Chart
        $panelsCount = InvoiceItem::whereHas('invoice', fn ($q) => $q->where('company_id', $tenant?->id)->where('status', '!=', 'cancelled'))
            ->where(fn ($q) => $q->where('description', 'like', '%panel%')->orWhere('description', 'like', '%module%'))
            ->count();
        $invertersCount = InvoiceItem::whereHas('invoice', fn ($q) => $q->where('company_id', $tenant?->id)->where('status', '!=', 'cancelled'))
            ->where('description', 'like', '%inverter%')
            ->count();
        $batteriesCount = InvoiceItem::whereHas('invoice', fn ($q) => $q->where('company_id', $tenant?->id)->where('status', '!=', 'cancelled'))
            ->where('description', 'like', '%battery%')
            ->count();
        $servicesCount = InvoiceItem::whereHas('invoice', fn ($q) => $q->where('company_id', $tenant?->id)->where('status', '!=', 'cancelled'))
            ->where(fn ($q) => $q->where('description', 'like', '%install%')->orWhere('description', 'like', '%cable%')->orWhere('description', 'like', '%breaker%')->orWhere('description', 'like', '%service%'))
            ->count();

        $totalCatItems = $panelsCount + $invertersCount + $batteriesCount + $servicesCount;
        if ($totalCatItems > 0) {
            $categoryShares = [
                min(100, round(($panelsCount / $totalCatItems) * 100)),
                min(100, round(($invertersCount / $totalCatItems) * 100)),
                min(100, round(($batteriesCount / $totalCatItems) * 100)),
                min(100, round(($servicesCount / $totalCatItems) * 100)),
            ];
        } else {
            $categoryShares = [0, 0, 0, 0];
        }

        return [
            'tenant' => $tenant,
            'company' => $tenant,
            'user' => $user,
            'isStaff' => false,
            'totalRevenue' => $totalRevenue,
            'quotationsCount' => $quotationsCount,
            'invoicesCount' => $invoicesCount,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'profitMargin' => $profitMargin,
            'materialCost' => $materialCost,
            'overheadAndCommissions' => $overheadAndCommissions,
            'pendingQuotationsCount' => $pendingQuotationsCount,
            'activeInstallationsCount' => $activeInstallationsCount,
            'commissionedSystemsCount' => $commissionedSystemsCount,
            'certificatesCount' => $certificatesCount,
            'allTransactions' => $allTransactions,
            'payments' => $payments,
            'expenses' => $expenses,
            'teamMembers' => $teamMembers,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlyCashflow' => $monthlyCashflow,
            'categoryShares' => $categoryShares,
        ];
    }
}
