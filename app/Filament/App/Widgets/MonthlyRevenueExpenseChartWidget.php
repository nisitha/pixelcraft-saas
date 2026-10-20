<?php

namespace App\Filament\App\Widgets;

use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class MonthlyRevenueExpenseChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $maxHeight = '260px';

    protected ?string $heading = 'Monthly Revenue & Expenses (Cash Flow)';

    protected ?string $description = 'Comparing received payments vs operational expenses over the last 6 months';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant?->id;

        $labels = [];
        $revenueData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth()->toDateString();
            $endOfMonth = $month->copy()->endOfMonth()->toDateString();

            $labels[] = $month->format('M Y');

            $revenue = Payment::where('company_id', $tenantId)
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $expense = Expense::where('company_id', $tenantId)
                ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $revenueData[] = (float) $revenue;
            $expenseData[] = (float) $expense;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue / Cash In (Rs.)',
                    'data' => $revenueData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.75)',
                    'borderColor' => '#059669',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Expenses / Cash Out (Rs.)',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.75)',
                    'borderColor' => '#dc2626',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
