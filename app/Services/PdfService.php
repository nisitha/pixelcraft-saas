<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateQuotationPdf(Quotation $quotation): DomPdfWrapper
    {
        $this->configureEnvironment();

        $quotation->loadMissing(['company', 'contact', 'items.product']);

        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $quotation->company,
            'contact' => $quotation->contact,
            'items' => $quotation->items,
        ])->setPaper('a4', 'portrait');

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    public function generateInvoicePdf(Invoice $invoice): DomPdfWrapper
    {
        $this->configureEnvironment();

        $invoice->loadMissing(['company', 'contact', 'quotation', 'items.product', 'payments']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'quotation' => $invoice->quotation,
            'company' => $invoice->company,
            'contact' => $invoice->contact,
            'items' => $invoice->items,
            'payments' => $invoice->payments,
        ])->setPaper('a4', 'portrait');

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    public function generatePosReceiptPdf(Invoice $invoice, ?float $amountTendered = null, ?float $changeDue = null): DomPdfWrapper
    {
        $this->configureEnvironment();

        $invoice->loadMissing(['company', 'contact', 'items.product', 'payments']);

        $pdf = Pdf::loadView('pdf.pos-receipt', [
            'invoice' => $invoice,
            'company' => $invoice->company,
            'contact' => $invoice->contact,
            'items' => $invoice->items,
            'payments' => $invoice->payments,
            'amountTendered' => $amountTendered ?? (float) $invoice->paid_amount,
            'changeDue' => $changeDue ?? 0.00,
        ])->setPaper([0, 0, 226.77, 650], 'portrait');

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    protected function configureEnvironment(): void
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $fontDir = storage_path('fonts');
        if (! file_exists($fontDir)) {
            @mkdir($fontDir, 0755, true);
        }
    }

    protected function applyDompdfOptions(DomPdfWrapper $pdf): void
    {
        $fontDir = storage_path('fonts');
        $publicDiskPath = Storage::disk('public')->path('');

        $chroot = array_values(array_filter([
            base_path(),
            public_path(),
            storage_path(),
            storage_path('app/public'),
            $publicDiskPath,
        ], fn ($path) => ! empty($path) && file_exists($path)));

        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'enable_remote' => true,
            'chroot' => $chroot,
            'font_dir' => $fontDir,
            'font_cache' => $fontDir,
            'temp_dir' => sys_get_temp_dir(),
        ], mergeWithDefaults: true);
    }
}
