<?php

namespace App\Observers;

use App\Filament\App\Pages\FinancialReports;
use App\Models\AuditLog;
use App\Models\Expense;
use Filament\Facades\Filament;

class ExpenseObserver
{
    /**
     * Helper to invalidate/flush financial report cache keys whenever an expense is mutated.
     */
    protected function invalidateFinancialReportsCache(?int $companyId): void
    {
        if ($companyId) {
            FinancialReports::flushCacheForTenant($companyId);
        }
    }

    public function created(Expense $expense): void
    {
        $this->invalidateFinancialReportsCache($expense->company_id);
    }

    public function updated(Expense $expense): void
    {
        $this->invalidateFinancialReportsCache($expense->company_id);
    }

    public function deleted(Expense $expense): void
    {
        $this->invalidateFinancialReportsCache($expense->company_id);

        $userId = Filament::auth()->id() ?? auth()->id();

        AuditLog::create([
            'company_id' => $expense->company_id,
            'user_id' => $userId,
            'action' => 'deleted',
            'model_type' => Expense::class,
            'model_id' => $expense->id,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
            'changes' => [
                'title' => $expense->title,
                'amount' => $expense->amount,
                'category' => $expense->category,
                'reference_no' => $expense->reference_no,
            ],
            'created_at' => now(),
        ]);
    }
}
