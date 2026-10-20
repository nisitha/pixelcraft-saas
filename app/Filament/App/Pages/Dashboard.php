<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\MonthlyRevenueExpenseChartWidget;
use App\Filament\App\Widgets\RecentInvoicesWidget;
use App\Filament\App\Widgets\RecentSolarQuotationsWidget;
use App\Filament\App\Widgets\SolarPipelineAndStockWidget;
use App\Filament\App\Widgets\ZohoReceivablesOverviewWidget;
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
            ZohoReceivablesOverviewWidget::class,
            MonthlyRevenueExpenseChartWidget::class,
            SolarPipelineAndStockWidget::class,
            RecentSolarQuotationsWidget::class,
            RecentInvoicesWidget::class,
        ];
    }
}
