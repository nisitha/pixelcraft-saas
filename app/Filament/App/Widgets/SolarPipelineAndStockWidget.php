<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\CommissionPayouts\CommissionPayoutResource;
use App\Filament\App\Resources\Products\ProductResource;
use App\Filament\App\Resources\Quotations\QuotationResource;
use App\Models\Invoice;
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

        // 1. Quotation Pipeline Breakdown
        $quotes = Quotation::where('company_id', $tenantId)->get();

        $draftQuotes = $quotes->where('status', 'draft');
        $sentQuotes = $quotes->where('status', 'sent');
        $acceptedQuotes = $quotes->where('status', 'accepted');
        $invoicedQuotes = $quotes->where('status', 'invoiced');

        $pipeline = [
            'draft' => [
                'count' => $draftQuotes->count(),
                'total' => $draftQuotes->sum('grand_total'),
            ],
            'sent' => [
                'count' => $sentQuotes->count(),
                'total' => $sentQuotes->sum('grand_total'),
            ],
            'accepted' => [
                'count' => $acceptedQuotes->count(),
                'total' => $acceptedQuotes->sum('grand_total'),
            ],
            'invoiced' => [
                'count' => $invoicedQuotes->count(),
                'total' => $invoicedQuotes->sum('grand_total'),
            ],
        ];

        // 2. Low Stock Alerts
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

        // 3. Pending Agent Commissions
        $pendingCommissionsCount = Invoice::where('company_id', $tenantId)
            ->whereNotNull('sales_agent_id')
            ->where('commission_status', 'pending')
            ->where('commission_amount', '>', 0)
            ->count();

        $pendingCommissionsAmount = (float) Invoice::where('company_id', $tenantId)
            ->whereNotNull('sales_agent_id')
            ->where('commission_status', 'pending')
            ->sum('commission_amount');

        return [
            'pipeline' => $pipeline,
            'lowStockProducts' => $lowStockProducts,
            'totalLowStockCount' => $totalLowStockCount,
            'pendingCommissionsCount' => $pendingCommissionsCount,
            'pendingCommissionsAmount' => $pendingCommissionsAmount,
            'quotationsUrl' => QuotationResource::getUrl('index'),
            'productsUrl' => ProductResource::getUrl('index'),
            'commissionsUrl' => CommissionPayoutResource::getUrl('index'),
        ];
    }
}
