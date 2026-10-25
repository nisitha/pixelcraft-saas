<?php

namespace App\Filament\App\Resources\SalaryAdvances\Pages;

use App\Filament\App\Resources\SalaryAdvances\SalaryAdvanceResource;
use App\Filament\App\Widgets\SalaryAdvancesOverviewWidget;
use App\Models\SalaryAdvance;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSalaryAdvances extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = SalaryAdvanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Advance Request')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SalaryAdvancesOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Requests')
                ->badge(fn () => SalaryAdvance::count()),

            'pending' => Tab::make('Pending Approval')
                ->badge(fn () => SalaryAdvance::where('status', 'pending')->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

            'approved' => Tab::make('Approved')
                ->badge(fn () => SalaryAdvance::where('status', 'approved')->count() ?: null)
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved')),

            'deducted' => Tab::make('Deducted (Recovered)')
                ->badge(fn () => SalaryAdvance::where('status', 'deducted')->count() ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'deducted')),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),
        ];
    }
}
