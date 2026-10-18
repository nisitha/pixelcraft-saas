<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Expense;
use Filament\Facades\Filament;

class ExpenseObserver
{
    public function deleted(Expense $expense): void
    {
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
