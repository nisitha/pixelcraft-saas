<?php

namespace App\Observers;

use App\Models\InvoiceItem;
use App\Models\Product;

class InvoiceItemObserver
{
    public function created(InvoiceItem $item): void
    {
        $invoice = $item->invoice;

        if ($invoice && $invoice->status === 'cancelled') {
            return;
        }

        $company = $invoice?->company;
        $inventoryEnabled = (bool) ($company?->settings['inventory_enabled'] ?? true);

        if (! $inventoryEnabled) {
            return;
        }

        if ($item->product_id) {
            $product = Product::find($item->product_id);
            if ($product && $product->type === 'product' && ($product->track_inventory ?? true)) {
                $product->decrement('stock_qty', (float) $item->quantity);
            }
        }
    }

    public function deleted(InvoiceItem $item): void
    {
        $invoice = $item->invoice;

        if ($invoice && $invoice->status === 'cancelled') {
            return;
        }

        $company = $invoice?->company;
        $inventoryEnabled = (bool) ($company?->settings['inventory_enabled'] ?? true);

        if (! $inventoryEnabled) {
            return;
        }

        if ($item->product_id) {
            $product = Product::find($item->product_id);
            if ($product && $product->type === 'product' && ($product->track_inventory ?? true)) {
                $product->increment('stock_qty', (float) $item->quantity);
            }
        }
    }
}
