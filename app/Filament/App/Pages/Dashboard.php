<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\AppQuickActionBarWidget;
use App\Filament\App\Widgets\AppStatsOverviewWidget;
use App\Filament\App\Widgets\MonthlyRevenueExpenseChartWidget;
use App\Filament\App\Widgets\RecentInvoicesWidget;
use App\Filament\App\Widgets\RecentSolarQuotationsWidget;
use App\Filament\App\Widgets\SolarPipelineAndStockWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public function getColumns(): int|array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            AppQuickActionBarWidget::class,
            AppStatsOverviewWidget::class,
            MonthlyRevenueExpenseChartWidget::class,
            SolarPipelineAndStockWidget::class,
            RecentSolarQuotationsWidget::class,
            RecentInvoicesWidget::class,
        ];
    }
}
