<?php

namespace App\Filament\App\Resources\Employees\Pages;

use App\Filament\App\Resources\Employees\EmployeeResource;
use App\Filament\App\Widgets\EmployeesOverviewWidget;
use App\Models\Employee;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Employee')
                ->icon('heroicon-o-user-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EmployeesOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Employees')
                ->badge(fn () => Employee::count()),

            'active' => Tab::make('Active Staff')
                ->badge(fn () => Employee::where('is_active', true)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),

            'technicians' => Tab::make('Technicians & Crew')
                ->badge(fn () => Employee::where('is_active', true)->whereIn('designation', ['Solar Technician', 'Site Supervisor', 'Electrician'])->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)->whereIn('designation', ['Solar Technician', 'Site Supervisor', 'Electrician'])),

            'office' => Tab::make('Office & Support')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)->whereNotIn('designation', ['Solar Technician', 'Site Supervisor', 'Electrician'])),

            'inactive' => Tab::make('Inactive')
                ->badge(fn () => Employee::where('is_active', false)->count() ?: null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),
        ];
    }
}
