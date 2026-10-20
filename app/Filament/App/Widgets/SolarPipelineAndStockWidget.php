<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\CommissionPayouts\CommissionPayoutResource;
use App\Filament\App\Resources\Products\ProductResource;
use App\Filament\App\Resources\Quotations\QuotationResource;
use App\Models\Product;
use App\Models\Quotation;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class SolarPipelineAndStockWidget extends Widget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected string $view = 'filament.app.widgets.solar-pipeline-and-stock';

    public function getViewData(): array
    {
        $tenant = Filament::getTenant();
        $tenantId = $tenant?->id;

        // 1. Quotation Pipeline & Conversion Rate
        $quotes = Quotation::where('company_id', $tenantId)->get();
        $totalQuotesCount = $quotes->count();

        $draftQuotes = $quotes->where('status', 'draft');
        $sentQuotes = $quotes->where('status', 'sent');
        $acceptedQuotes = $quotes->where('status', 'accepted');
        $invoicedQuotes = $quotes->where('status', 'invoiced');

        $invoicedCount = $invoicedQuotes->count();
        $conversionRate = $totalQuotesCount > 0 ? round(($invoicedCount / $totalQuotesCount) * 100, 1) : 0.0;

        $pipeline = [
            'draft' => [
                'count' => $draftQuotes->count(),
                'total' => (float) $draftQuotes->sum('grand_total'),
            ],
            'sent' => [
                'count' => $sentQuotes->count(),
                'total' => (float) $sentQuotes->sum('grand_total'),
            ],
            'accepted' => [
                'count' => $acceptedQuotes->count(),
                'total' => (float) $acceptedQuotes->sum('grand_total'),
            ],
            'invoiced' => [
                'count' => $invoicedCount,
                'total' => (float) $invoicedQuotes->sum('grand_total'),
            ],
        ];

        // 2. Recent Quotations for Linear Activity Feed
        $recentQuotations = Quotation::with('contact')
            ->where('company_id', $tenantId)
            ->latest('id')
            ->limit(5)
            ->get();

        // 3. Low Stock Alerts
        $lowStockProducts = Product::where('company_id', $tenantId)
            ->where('type', 'product')
            ->whereColumn('stock_qty', '<=', 'alert_qty')
            ->orderBy('stock_qty', 'asc')
            ->limit(3)
            ->get();

        $totalLowStockCount = Product::where('company_id', $tenantId)
            ->where('type', 'product')
            ->whereColumn('stock_qty', '<=', 'alert_qty')
            ->count();

        return [
            'totalQuotesCount' => $totalQuotesCount,
            'invoicedCount' => $invoicedCount,
            'conversionRate' => $conversionRate,
            'pipeline' => $pipeline,
            'recentQuotations' => $recentQuotations,
            'lowStockProducts' => $lowStockProducts,
            'totalLowStockCount' => $totalLowStockCount,
            'quotationsUrl' => QuotationResource::getUrl('index'),
            'productsUrl' => ProductResource::getUrl('index'),
            'commissionsUrl' => CommissionPayoutResource::getUrl('index'),
        ];
    }
}
