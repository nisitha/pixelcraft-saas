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

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant?->id;

        $totalRevenue = Payment::where('company_id', $tenantId)->sum('amount');
        if ($totalRevenue == 0) {
            $totalRevenue = Invoice::where('company_id', $tenantId)->sum('paid_amount');
        }

        $totalQuotations = Quotation::where('company_id', $tenantId)->count();
        $draftQuotes = Quotation::where('company_id', $tenantId)->where('status', 'draft')->count();
        $sentQuotes = Quotation::where('company_id', $tenantId)->where('status', 'sent')->count();
        $acceptedQuotes = Quotation::where('company_id', $tenantId)->where('status', 'accepted')->count();

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
        $inStockCount = Product::where('company_id', $tenantId)
            ->where('type', 'product')
            ->whereColumn('stock_qty', '>', 'alert_qty')
            ->count();

        return [
            Stat::make('Total Revenue', 'Rs. '.number_format((float) $totalRevenue, 2))
                ->description('+12.4% vs last mo · Collected')
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->color('success')
                ->chart([28, 38, 45, 52, 68, 85, 95]),

            Stat::make('Total Quotations', (string) $totalQuotations)
                ->description("{$draftQuotes} Draft · {$sentQuotes} Sent · {$acceptedQuotes} Accepted")
                ->descriptionIcon(Heroicon::DocumentText)
                ->color('info')
                ->chart([12, 18, 15, 24, 28, 35, max(8, (int) $totalQuotations)]),

            Stat::make('Pending Invoices', 'Rs. '.number_format((float) $pendingDueAmount, 2))
                ->description($pendingInvoicesCount.' unpaid '.($pendingInvoicesCount === 1 ? 'invoice' : 'invoices'))
                ->descriptionIcon(Heroicon::Clock)
                ->color('warning')
                ->chart([15, 22, 19, 25, 20, 26, max(6, (int) $pendingInvoicesCount)]),

            Stat::make('Low Stock Products', (string) $lowStockCount)
                ->description($lowStockCount > 0 ? "{$lowStockCount} below threshold · {$inStockCount} in stock" : "{$inStockCount} items in stock · Optimal")
                ->descriptionIcon($lowStockCount > 0 ? Heroicon::ExclamationTriangle : Heroicon::CheckCircle)
                ->color($lowStockCount > 0 ? 'danger' : 'success')
                ->chart([3, 5, 2, 4, 3, 2, max(1, (int) $lowStockCount)]),
        ];
    }
}
