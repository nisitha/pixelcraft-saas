<?php

namespace App\Filament\App\Widgets;

use App\Models\Expense;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class ExpensesOverviewWidget extends Widget
{
    protected string $view = 'filament.app.widgets.expenses-overview';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $tenant = Filament::getTenant();

        $thisMonthExpenses = (float) Expense::where('company_id', $tenant?->id)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $equipmentSupplierTotal = (float) Expense::where('company_id', $tenant?->id)
            ->where(function ($q) {
                $q->where('category', 'like', '%equipment%')
                    ->orWhere('category', 'like', '%supplier%')
                    ->orWhere('category', 'like', '%material%')
                    ->orWhere('category', 'like', '%hardware%')
                    ->orWhere('category', 'like', '%purchase%');
            })
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $labourInstallationTotal = (float) Expense::where('company_id', $tenant?->id)
            ->where(function ($q) {
                $q->where('category', 'like', '%install%')
                    ->orWhere('category', 'like', '%labour%')
                    ->orWhere('category', 'like', '%labor%')
                    ->orWhere('category', 'like', '%food%')
                    ->orWhere('category', 'like', '%transport%')
                    ->orWhere('category', 'like', '%site%');
            })
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $operationalOverheadTotal = (float) Expense::where('company_id', $tenant?->id)
            ->where(function ($q) {
                $q->where('category', 'like', '%office%')
                    ->orWhere('category', 'like', '%utility%')
                    ->orWhere('category', 'like', '%overhead%')
                    ->orWhere('category', 'like', '%rent%')
                    ->orWhere('category', 'like', '%salary%')
                    ->orWhere('category', 'like', '%bill%')
                    ->orWhere('category', 'like', '%other%');
            })
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return [
            'thisMonthExpenses' => $thisMonthExpenses,
            'equipmentSupplierTotal' => $equipmentSupplierTotal,
            'labourInstallationTotal' => $labourInstallationTotal,
            'operationalOverheadTotal' => $operationalOverheadTotal,
        ];
    }
}
