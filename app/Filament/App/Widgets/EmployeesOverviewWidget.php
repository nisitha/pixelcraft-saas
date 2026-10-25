<?php

namespace App\Filament\App\Widgets;

use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeesOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $tenantId = Filament::getTenant()?->id;

        $totalEmployees = Employee::where('company_id', $tenantId)->count();
        $activeEmployees = Employee::where('company_id', $tenantId)->where('is_active', true)->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        $totalBasic = (float) Employee::where('company_id', $tenantId)->where('is_active', true)->sum('basic_salary');
        $totalAllowances = (float) Employee::where('company_id', $tenantId)->where('is_active', true)->sum('fixed_allowance');
        $grossMonthlyLiability = $totalBasic + $totalAllowances;

        $techCount = Employee::where('company_id', $tenantId)
            ->where('is_active', true)
            ->whereIn('designation', ['Solar Technician', 'Site Supervisor', 'Electrician'])
            ->count();
        $officeCount = max(0, $activeEmployees - $techCount);

        return [
            Stat::make('Workforce Total', (string) $activeEmployees)
                ->description("{$activeEmployees} Active · {$inactiveEmployees} Inactive")
                ->descriptionIcon(Heroicon::UserGroup)
                ->color('primary')
                ->chart([3, 5, 6, 8, 10, max(5, $activeEmployees)]),

            Stat::make('Monthly Basic Payroll', 'Rs. '.number_format($totalBasic, 2))
                ->description('Contracted base salaries')
                ->descriptionIcon(Heroicon::Banknotes)
                ->color('info')
                ->chart([10, 20, 30, 40, 50, 60]),

            Stat::make('Gross Monthly Commitment', 'Rs. '.number_format($grossMonthlyLiability, 2))
                ->description('Includes fixed allowances')
                ->descriptionIcon(Heroicon::CurrencyDollar)
                ->color('success')
                ->chart([15, 25, 35, 45, 55, 70]),

            Stat::make('Technical Crew', "{$techCount} Technicians")
                ->description("{$techCount} Field Crew · {$officeCount} Office/Support")
                ->descriptionIcon(Heroicon::WrenchScrewdriver)
                ->color('warning')
                ->chart([2, 4, 3, 5, 4, max(3, $techCount)]),
        ];
    }
}
