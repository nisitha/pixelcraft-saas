<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Pages\PosTerminal;
use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Filament\App\Resources\Expenses\ExpenseResource;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Filament\App\Resources\Payments\PaymentResource;
use App\Filament\App\Resources\Quotations\QuotationResource;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class ZohoReceivablesOverviewWidget extends Widget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.zoho-receivables-overview';

    public function getViewData(): array
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant?->id;
        $user = Filament::auth()->user();
        $today = Carbon::today();

        // 1. Receivables Calculation
        $unpaidInvoices = Invoice::where('company_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();

        $totalReceivables = 0.00;
        $currentAmount = 0.00;
        $overdue1To15 = 0.00;
        $overdue16To30 = 0.00;
        $overdueOver30 = 0.00;

        foreach ($unpaidInvoices as $inv) {
            $due = (float) $inv->due_amount;
            $totalReceivables += $due;

            $dueDate = $inv->due_date ? Carbon::parse($inv->due_date)->startOfDay() : $today;

            if ($dueDate->gte($today)) {
                $currentAmount += $due;
            } else {
                $daysOverdue = $today->diffInDays($dueDate);
                if ($daysOverdue <= 15) {
                    $overdue1To15 += $due;
                } elseif ($daysOverdue <= 30) {
                    $overdue16To30 += $due;
                } else {
                    $overdueOver30 += $due;
                }
            }
        }

        // Percentages for progress bar
        $currentPct = $totalReceivables > 0 ? round(($currentAmount / $totalReceivables) * 100, 1) : 0;
        $overdue1To15Pct = $totalReceivables > 0 ? round(($overdue1To15 / $totalReceivables) * 100, 1) : 0;
        $overdue16To30Pct = $totalReceivables > 0 ? round(($overdue16To30 / $totalReceivables) * 100, 1) : 0;
        $overdueOver30Pct = $totalReceivables > 0 ? round(($overdueOver30 / $totalReceivables) * 100, 1) : 0;

        // 2. Monthly Cash Flow & Profit
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $totalCollectedThisMonth = (float) Payment::where('company_id', $tenantId)
            ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        if ($totalCollectedThisMonth == 0) {
            $totalCollectedThisMonth = (float) Invoice::where('company_id', $tenantId)
                ->whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
                ->sum('paid_amount');
        }

        $totalExpensesThisMonth = (float) Expense::where('company_id', $tenantId)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $netProfitThisMonth = $totalCollectedThisMonth - $totalExpensesThisMonth;

        $isOwnerOrAdmin = $user ? $user->isTenantOwnerOrAdmin($tenant) : false;
        $canAccessPos = ! ($user?->isTenantEngineer($tenant)) && (bool) ($tenant?->settings['enable_pos_module'] ?? true);
        $canAccessPayments = ! ($user?->isTenantEngineer($tenant));
        $canAccessExpenses = $isOwnerOrAdmin;
        $canAccessCertificates = ! ($user?->isTenantCashier($tenant));
        $canAccessQuotations = ! ($user?->isTenantEngineer($tenant)) && ! ($user?->isTenantCashier($tenant));

        return [
            'totalReceivables' => $totalReceivables,
            'currentAmount' => $currentAmount,
            'currentPct' => $currentPct,
            'overdue1To15' => $overdue1To15,
            'overdue1To15Pct' => $overdue1To15Pct,
            'overdue16To30' => $overdue16To30,
            'overdue16To30Pct' => $overdue16To30Pct,
            'overdueOver30' => $overdueOver30,
            'overdueOver30Pct' => $overdueOver30Pct,
            'totalCollectedThisMonth' => $totalCollectedThisMonth,
            'netProfitThisMonth' => $netProfitThisMonth,
            'isOwnerOrAdmin' => $isOwnerOrAdmin,
            'canAccessPos' => $canAccessPos,
            'canAccessPayments' => $canAccessPayments,
            'canAccessExpenses' => $canAccessExpenses,
            'canAccessCertificates' => $canAccessCertificates,
            'canAccessQuotations' => $canAccessQuotations,
            'newQuotationUrl' => QuotationResource::getUrl('create'),
            'newInvoiceUrl' => InvoiceResource::getUrl('create'),
            'posTerminalUrl' => PosTerminal::getUrl(),
            'recordPaymentUrl' => PaymentResource::getUrl('create'),
            'logExpenseUrl' => ExpenseResource::getUrl('create'),
            'issueCertificateUrl' => CompletionCertificateResource::getUrl('create'),
        ];
    }
}
