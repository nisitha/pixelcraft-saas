<?php

namespace App\Filament\App\Resources\Payments\Pages;

use App\Filament\App\Resources\Payments\PaymentResource;
use App\Filament\App\Widgets\PaymentsOverviewWidget;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentsOverviewWidget::class,
        ];
    }

    public function applyTableFilters(): void
    {
        parent::applyTableFilters();
        $this->dispatch('updatePaymentStats');
    }

    public function resetTableFiltersForm(): void
    {
        parent::resetTableFiltersForm();
        $this->dispatch('updatePaymentStats');
    }

    public function updatedTableFilters(): void
    {
        parent::updatedTableFilters();
        $this->dispatch('updatePaymentStats');
    }

    public function updatedTableSearch(): void
    {
        parent::updatedTableSearch();
        $this->dispatch('updatePaymentStats');
    }

    public function updatedTableSort(): void
    {
        parent::updatedTableSort();
        $this->dispatch('updatePaymentStats');
    }

    public function updatedTableSortDirection(): void
    {
        parent::updatedTableSortDirection();
        $this->dispatch('updatePaymentStats');
    }

    public function updatedActiveTab(): void
    {
        parent::updatedActiveTab();
        $this->dispatch('updatePaymentStats');
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Payments'),
            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('payment_date', today())),
            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()])),
            'this_month' => Tab::make('This Month')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)),
            'high_value' => Tab::make('High Value (> Rs. 50K)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('amount', '>=', 50000)),
        ];
    }
}
