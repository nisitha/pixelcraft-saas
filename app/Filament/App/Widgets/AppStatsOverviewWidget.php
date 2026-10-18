<?php

namespace App\Filament\App\Widgets;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AppStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant?->id;

        $totalRevenue = Payment::where('company_id', $tenantId)->sum('amount');
        if ($totalRevenue == 0) {
            $totalRevenue = Invoice::where('company_id', $tenantId)->sum('paid_amount');
        }

        $totalQuotations = Quotation::where('company_id', $tenantId)->count();
        $pendingInvoicesCount = Invoice::where('company_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->count();
        $pendingDueAmount = Invoice::where('company_id', $tenantId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('due_amount');

        $lowStockCount = Product::where('company_id', $tenantId)
            ->where('type', 'product')
            ->whereColumn('stock_qty', '<=', 'alert_qty')
            ->count();

        return [
            Stat::make('Total Revenue', 'Rs. '.number_format((float) $totalRevenue, 2))
                ->description('Collected revenue')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->color('success')
                ->chart([35, 45, 60, 50, 75, 90]),

            Stat::make('Total Quotations', (string) $totalQuotations)
                ->description('Active solar & EPC proposals')
                ->descriptionIcon(Heroicon::DocumentText)
                ->color('info'),

            Stat::make('Pending Invoices', (string) $pendingInvoicesCount)
                ->description('Rs. '.number_format((float) $pendingDueAmount, 2).' outstanding')
                ->descriptionIcon(Heroicon::Clock)
                ->color('warning'),

            Stat::make('Low Stock Products', (string) $lowStockCount)
                ->description('Items below alert threshold')
                ->descriptionIcon(Heroicon::ExclamationTriangle)
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}
