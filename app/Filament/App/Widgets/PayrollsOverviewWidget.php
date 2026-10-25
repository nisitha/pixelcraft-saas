<?php

namespace App\Filament\App\Widgets;

use App\Models\Payroll;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PayrollsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()?->id;

        $latestPayroll = Payroll::where('company_id', $tenantId)
            ->orderByDesc('month')
            ->first();

        $latestMonthLabel = $latestPayroll ? "{$latestPayroll->month} (".ucfirst($latestPayroll->status).')' : 'No runs yet';
        $latestNet = (float) ($latestPayroll?->total_net_payable ?? 0);

        // YTD Net Paid
        $currentYear = now()->year;
        $ytdNetPaid = (float) Payroll::where('company_id', $tenantId)
            ->where('status', 'paid')
            ->where('month', 'like', "{$currentYear}-%")
            ->sum('total_net_payable');

        // YTD Statutory Remittance (EPF 8% + 12% + ETF 3%)
        $epfEmpr = (float) Payroll::where('company_id', $tenantId)
            ->whereIn('status', ['paid', 'confirmed'])
            ->where('month', 'like', "{$currentYear}-%")
            ->sum('total_epf_employer');

        $etfEmpr = (float) Payroll::where('company_id', $tenantId)
            ->whereIn('status', ['paid', 'confirmed'])
            ->where('month', 'like', "{$currentYear}-%")
            ->sum('total_etf_employer');

        $epfEmp = (float) Payroll::where('company_id', $tenantId)
            ->whereIn('status', ['paid', 'confirmed'])
            ->where('month', 'like', "{$currentYear}-%")
            ->sum('total_epf_employee');

        $ytdStatutory = round($epfEmpr + $etfEmpr + $epfEmp, 2);

        // Total Advances Recovered YTD
        $ytdAdvances = (float) Payroll::where('company_id', $tenantId)
            ->whereIn('status', ['paid', 'confirmed'])
            ->where('month', 'like', "{$currentYear}-%")
            ->sum('total_advances');

        return [
            Stat::make('Latest Run', $latestMonthLabel)
                ->description($latestPayroll ? 'Rs. '.number_format($latestNet, 2).' net payable' : 'Create monthly payroll batch')
                ->descriptionIcon(Heroicon::Calendar)
                ->color($latestPayroll?->status === 'paid' ? 'success' : ($latestPayroll?->status === 'confirmed' ? 'info' : 'warning'))
                ->chart([5, 8, 12, 10, 15, max(5, (int) ($latestPayroll?->items_count ?? 1))]),

            Stat::make('YTD Net Disbursed', 'Rs. '.number_format($ytdNetPaid, 2))
                ->description("Paid staff remuneration in {$currentYear}")
                ->descriptionIcon(Heroicon::Banknotes)
                ->color('primary')
                ->chart([20, 35, 45, 60, 75, 90]),

            Stat::make('YTD Statutory Remittance', 'Rs. '.number_format($ytdStatutory, 2))
                ->description('EPF 8% + 12% + ETF 3%')
                ->descriptionIcon(Heroicon::BuildingLibrary)
                ->color('info')
                ->chart([10, 20, 30, 40, 50, 65]),

            Stat::make('YTD Advances Recovered', 'Rs. '.number_format($ytdAdvances, 2))
                ->description('Staff loan/advance deductions')
                ->descriptionIcon(Heroicon::ArrowPath)
                ->color('success')
                ->chart([5, 8, 12, 15, 20, 25]),
        ];
    }
}
