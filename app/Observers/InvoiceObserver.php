<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Facades\Filament;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        $userId = Filament::auth()->id() ?? auth()->id();

        AuditLog::create([
            'company_id' => $invoice->company_id,
            'user_id' => $userId,
            'action' => 'created',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
            'changes' => [
                'invoice_number' => $invoice->invoice_number,
                'grand_total' => $invoice->grand_total,
                'status' => $invoice->status,
                'type' => $invoice->type,
            ],
            'created_at' => now(),
        ]);
    }

    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('status')) {
            $newStatus = $invoice->status;
            $oldStatus = $invoice->getOriginal('status');
            $userId = Filament::auth()->id() ?? auth()->id();

            // Invoice Cancelled: Restore inventory stock and release serials
            if ($newStatus === 'cancelled') {
                foreach ($invoice->items as $item) {
                    if ($item->product_id) {
                        $product = Product::find($item->product_id);
                        if ($product && $product->type === 'product') {
                            $product->increment('stock_qty', (float) $item->quantity);
                        }
                    }
                }

                // Release any linked serials back to in_stock
                $invoice->serials()->update([
                    'status' => 'in_stock',
                    'contact_id' => null,
                    'invoice_id' => null,
                ]);

                AuditLog::create([
                    'company_id' => $invoice->company_id,
                    'user_id' => $userId,
                    'action' => 'cancelled',
                    'model_type' => Invoice::class,
                    'model_id' => $invoice->id,
                    'ip_address' => request()?->ip() ?? '127.0.0.1',
                    'changes' => [
                        'invoice_number' => $invoice->invoice_number,
                        'previous_status' => $oldStatus,
                        'status' => 'cancelled',
                    ],
                    'created_at' => now(),
                ]);
            }

            // Un-cancelling an invoice: Re-deduct stock
            if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($invoice->items as $item) {
                    if ($item->product_id) {
                        $product = Product::find($item->product_id);
                        if ($product && $product->type === 'product') {
                            $product->decrement('stock_qty', (float) $item->quantity);
                        }
                    }
                }
            }
        }
    }
}
