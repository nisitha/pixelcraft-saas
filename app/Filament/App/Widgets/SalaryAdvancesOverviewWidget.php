<?php

namespace App\Filament\App\Widgets;

use App\Models\SalaryAdvance;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalaryAdvancesOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()?->id;

        $pendingAmount = (float) SalaryAdvance::where('company_id', $tenantId)
            ->where('status', 'pending')
            ->sum('amount');
        $pendingCount = SalaryAdvance::where('company_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        $currentMonth = now()->format('Y-m');
        $approvedAmount = (float) SalaryAdvance::where('company_id', $tenantId)
            ->where('status', 'approved')
            ->where('deduct_month', $currentMonth)
            ->sum('amount');

        $deductedAmount = (float) SalaryAdvance::where('company_id', $tenantId)
            ->where('status', 'deducted')
            ->sum('amount');

        $activeStaffCount = SalaryAdvance::where('company_id', $tenantId)
            ->distinct('employee_id')
            ->count('employee_id');

        return [
            Stat::make('Pending Requests', 'Rs. '.number_format($pendingAmount, 2))
                ->description("{$pendingCount} requests awaiting approval")
                ->descriptionIcon(Heroicon::Clock)
                ->color($pendingCount > 0 ? 'warning' : 'gray')
                ->chart([2, 5, 3, 6, 4, max(2, $pendingCount)]),

            Stat::make('Approved (This Month)', 'Rs. '.number_format($approvedAmount, 2))
                ->description("To deduct in {$currentMonth} payroll")
                ->descriptionIcon(Heroicon::CheckCircle)
                ->color('info')
                ->chart([5, 10, 8, 15, 12, 20]),

            Stat::make('Total Recovered (YTD)', 'Rs. '.number_format($deductedAmount, 2))
                ->description('Successfully deducted from payroll')
                ->descriptionIcon(Heroicon::ArrowPath)
                ->color('success')
                ->chart([10, 20, 30, 45, 60, 80]),

            Stat::make('Staff Beneficiaries', "{$activeStaffCount} Employees")
                ->description('Employees with advance records')
                ->descriptionIcon(Heroicon::Users)
                ->color('primary')
                ->chart([1, 2, 2, 3, 4, max(2, $activeStaffCount)]),
        ];
    }
}
