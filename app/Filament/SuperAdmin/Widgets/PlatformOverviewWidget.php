<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();

        $gmv = (float) Invoice::sum('paid_amount');

        return [
            Stat::make('Total Companies', (string) $totalCompanies)
                ->description("{$activeCompanies} active tenants")
                ->descriptionIcon(Heroicon::OutlinedBuildingOffice2)
                ->color('primary'),

            Stat::make('Total Users', (string) $totalUsers)
                ->description("{$activeUsers} active accounts")
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('success'),

            Stat::make('Total Paid Volume (GMV)', 'Rs. '.number_format($gmv, 2))
                ->description('Platform-wide collections')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('warning'),
        ];
    }
}
