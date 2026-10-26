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

    protected ?string $maxHeight = '220px';

    protected ?string $heading = 'Monthly Cash Flow';

    protected ?string $description = 'Comparing received revenue vs operational expenses (Last 6 Months)';

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
                    'backgroundColor' => 'rgba(70, 128, 255, 0.85)',
                    'borderColor' => '#4680ff',
                    'borderWidth' => 1.5,
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Expenses / Cash Out (Rs.)',
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(137, 155, 181, 0.65)',
                    'borderColor' => '#899bb5',
                    'borderWidth' => 1.5,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'align' => 'end',
                    'labels' => [
                        'boxWidth' => 8,
                        'boxHeight' => 8,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'y' => [
                    'border' => [
                        'dash' => [3, 3],
                    ],
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.05)',
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
