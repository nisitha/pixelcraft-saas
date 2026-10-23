<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\Payments\Pages\ListPayments;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;

class PaymentsOverviewWidget extends Widget
{
    use InteractsWithPageTable;

    protected string $view = 'filament.app.widgets.payments-overview';

    protected int|string|array $columnSpan = 'full';

    protected function getTablePage(): string
    {
        return ListPayments::class;
    }

    /**
     * Listen to table filter and sorting events to refresh stats reactively without page reloads.
     *
     * @var array<string, string>
     */
    protected $listeners = [
        'tableFiltersApplied' => '$refresh',
        'tableFiltersReset' => '$refresh',
        'tableSearchApplied' => '$refresh',
        'tableSortApplied' => '$refresh',
        'activeTabChanged' => '$refresh',
        'updatePaymentStats' => '$refresh',
    ];

    #[On('tableFiltersApplied')]
    #[On('tableFiltersReset')]
    #[On('tableSearchApplied')]
    #[On('tableSortApplied')]
    #[On('activeTabChanged')]
    #[On('updatePaymentStats')]
    public function refreshStats(): void
    {
        // Re-renders widget reactively when table filters, search, or tabs change
    }

    public function getViewData(): array
    {
        // 1. Obtain query scoped to active table filters, search, tabs, & sorts
        $query = $this->getPageTableQuery();

        // 2. Card 1: Total Payments Collected (Honoring status = completed if column exists)
        $completedQuery = clone $query;
        if (Schema::hasColumn('payments', 'status')) {
            $completedQuery->where('payments.status', 'completed');
        }
        $totalCollected = (float) $completedQuery->sum('payments.amount');

        // 3. Card 2: Transactions Count
        $transactionsCount = (int) (clone $query)->count();

        // 4. Card 3: Bank Transfer vs Cash Breakdown
        $bankTransferTotal = (float) (clone $query)
            ->whereIn('payments.method', ['bank_transfer', 'cheque', 'online', 'card'])
            ->sum('payments.amount');

        $cashPosTotal = (float) (clone $query)
            ->whereIn('payments.method', ['cash', 'pos'])
            ->sum('payments.amount');

        $totalBreakdown = $bankTransferTotal + $cashPosTotal;
        $bankPercentage = $totalBreakdown > 0 ? round(($bankTransferTotal / $totalBreakdown) * 100, 1) : 0.0;
        $cashPercentage = $totalBreakdown > 0 ? round(($cashPosTotal / $totalBreakdown) * 100, 1) : 0.0;

        // Additional helpful metrics for dashboard
        $todayTotal = (float) (clone $query)
            ->whereDate('payments.payment_date', today())
            ->sum('payments.amount');

        $avgTransaction = $transactionsCount > 0 ? round($totalCollected / $transactionsCount, 2) : 0.0;

        return [
            'totalCollected' => $totalCollected,
            'transactionsCount' => $transactionsCount,
            'bankTransferTotal' => $bankTransferTotal,
            'cashPosTotal' => $cashPosTotal,
            'bankPercentage' => $bankPercentage,
            'cashPercentage' => $cashPercentage,
            'thisMonthTotal' => $totalCollected,
            'lastMonthTotal' => 0.0,
            'trend' => 0.0,
            'todayTotal' => $todayTotal,
            'bankCardTotal' => $bankTransferTotal,
            'avgTransaction' => $avgTransaction,
        ];
    }
}
