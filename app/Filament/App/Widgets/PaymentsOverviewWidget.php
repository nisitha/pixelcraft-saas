<?php

namespace App\Filament\App\Widgets;

use App\Models\Payment;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class PaymentsOverviewWidget extends Widget
{
    protected string $view = 'filament.app.widgets.payments-overview';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $tenant = Filament::getTenant();

        $thisMonthTotal = (float) Payment::where('company_id', $tenant?->id)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $lastMonthTotal = (float) Payment::where('company_id', $tenant?->id)
            ->whereMonth('payment_date', now()->subMonth()->month)
            ->whereYear('payment_date', now()->subMonth()->year)
            ->sum('amount');

        $trend = $lastMonthTotal > 0 ? round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1) : 0;

        $todayTotal = (float) Payment::where('company_id', $tenant?->id)
            ->whereDate('payment_date', today())
            ->sum('amount');

        $cashPosTotal = (float) Payment::where('company_id', $tenant?->id)
            ->whereIn('method', ['cash', 'pos'])
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $bankCardTotal = (float) Payment::where('company_id', $tenant?->id)
            ->whereIn('method', ['bank_transfer', 'cheque', 'online', 'card'])
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        return [
            'thisMonthTotal' => $thisMonthTotal,
            'lastMonthTotal' => $lastMonthTotal,
            'trend' => $trend,
            'todayTotal' => $todayTotal,
            'cashPosTotal' => $cashPosTotal,
            'bankCardTotal' => $bankCardTotal,
        ];
    }
}
