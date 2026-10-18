<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Payment;
use Filament\Facades\Filament;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        $userId = Filament::auth()->id() ?? auth()->id();

        AuditLog::create([
            'company_id' => $payment->company_id,
            'user_id' => $userId,
            'action' => 'created',
            'model_type' => Payment::class,
            'model_id' => $payment->id,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
            'changes' => [
                'invoice_id' => $payment->invoice_id,
                'amount' => $payment->amount,
                'method' => $payment->method,
                'reference_no' => $payment->reference_no,
                'milestone_label' => $payment->milestone_label,
            ],
            'created_at' => now(),
        ]);
    }
}
