<?php

namespace App\Services;

use App\Models\CommissionPayout;
use App\Models\CompletionCertificate;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateQuotationPdf(Quotation $quotation, ?string $mode = null): DomPdfWrapper
    {
        $this->configureEnvironment();

        $mode ??= $quotation->price_display_mode ?? 'itemized';
        $quotation->loadMissing(['company', 'contact', 'items.product']);

        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation,
            'company' => $quotation->company,
            'contact' => $quotation->contact,
            'items' => $quotation->items,
            'mode' => $mode,
        ])->setPaper('a4', 'portrait');

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    public function generateInvoicePdf(Invoice $invoice, ?string $mode = null): DomPdfWrapper
    {
        $this->configureEnvironment();

        $mode ??= $invoice->price_display_mode ?? 'itemized';
        $invoice->loadMissing(['company', 'contact', 'quotation', 'items.product', 'payments']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'quotation' => $invoice->quotation,
            'company' => $invoice->company,
            'contact' => $invoice->contact,
            'items' => $invoice->items,
            'payments' => $invoice->payments,
            'mode' => $mode,
        ])->setPaper('a4', 'portrait');

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    public function generateDeliveryNotePdf(Invoice|Quotation $record, ?string $vehicleNo = null, ?string $driverName = null): DomPdfWrapper
    {
        $this->configureEnvironment();

        $record->loadMissing(['company', 'contact', 'items.product']);

        $referenceNumber = $record instanceof Invoice ? $record->invoice_number : $record->quote_number;

        $pdf = Pdf::loadView('pdf.delivery-note', [
            'record' => $record,
            'company' => $record->company,
            'contact' => $record->contact,
            'items' => $record->items,
            'referenceNumber' => $referenceNumber,
            'vehicleNo' => $vehicleNo,
            'driverName' => $driverName,
        ])->setPaper('a4', 'portrait');

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    public function generatePaymentReceiptPdf(Payment $payment): DomPdfWrapper
    {
        $this->configureEnvironment();

        $payment->loadMissing(['company', 'invoice.contact']);

        $pdf = Pdf::loadView('pdf.payment-receipt', [
            'payment' => $payment,
            'company' => $payment->company,
            'invoice' => $payment->invoice,
            'contact' => $payment->invoice?->contact,
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

    public static function sanitizeFileName(string $name): string
    {
        return str_replace(['/', '\\'], '-', $name);
    }

    public static function getQuotationFileName(Quotation|string $quotation): string
    {
        $number = $quotation instanceof Quotation ? $quotation->quote_number : $quotation;
        $safeNumber = static::sanitizeFileName($number);

        return "Quotation-{$safeNumber}.pdf";
    }

    public static function getInvoiceFileName(Invoice|string $invoice): string
    {
        $number = $invoice instanceof Invoice ? $invoice->invoice_number : $invoice;
        $safeNumber = static::sanitizeFileName($number);

        return "Invoice-{$safeNumber}.pdf";
    }

    public static function getPosReceiptFileName(Invoice|string $invoice): string
    {
        $number = $invoice instanceof Invoice ? $invoice->invoice_number : $invoice;
        $safeNumber = static::sanitizeFileName($number);

        return "Receipt-{$safeNumber}.pdf";
    }

    public static function getDeliveryNoteFileName(Invoice|Quotation|string $record): string
    {
        $number = is_string($record)
            ? $record
            : ($record instanceof Invoice ? $record->invoice_number : $record->quote_number);
        $safeNumber = static::sanitizeFileName($number);

        return "DeliveryNote-{$safeNumber}.pdf";
    }

    public static function getPaymentReceiptFileName(Payment|string $payment): string
    {
        $number = is_string($payment)
            ? $payment
            : ($payment->reference_no ?: ('REC-'.$payment->id));
        $safeNumber = static::sanitizeFileName($number);

        return "PaymentReceipt-{$safeNumber}.pdf";
    }

    public function generateCompletionCertificatePdf(CompletionCertificate $certificate): DomPdfWrapper
    {
        $this->configureEnvironment();

        $certificate->loadMissing(['company', 'contact', 'invoice', 'quotation']);

        $paperSize = strtolower($certificate->paper_size ?? 'a4');
        $orientation = strtolower($certificate->orientation ?? 'landscape');

        $pdf = Pdf::loadView('pdf.completion-certificate', [
            'certificate' => $certificate,
            'company' => $certificate->company,
            'contact' => $certificate->contact,
            'invoice' => $certificate->invoice,
            'quotation' => $certificate->quotation,
        ])->setPaper($paperSize, $orientation);

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    public static function getCompletionCertificateFileName(CompletionCertificate|string $certificate): string
    {
        $number = $certificate instanceof CompletionCertificate ? $certificate->certificate_number : $certificate;
        $safeNumber = static::sanitizeFileName($number);

        return "Certificate-{$safeNumber}.pdf";
    }

    public function generateCommissionVoucherPdf(CommissionPayout $payout): DomPdfWrapper
    {
        $this->configureEnvironment();

        $payout->loadMissing(['company', 'salesAgent', 'invoice.contact']);

        $pdf = Pdf::loadView('pdf.commission-voucher', [
            'payout' => $payout,
            'company' => $payout->company,
            'agent' => $payout->salesAgent,
            'invoice' => $payout->invoice,
        ])->setPaper('a5', 'landscape');

        $this->applyDompdfOptions($pdf);

        return $pdf;
    }

    public static function getCommissionVoucherFileName(CommissionPayout|string $payout): string
    {
        $number = $payout instanceof CommissionPayout ? $payout->voucher_number : $payout;
        $safeNumber = static::sanitizeFileName($number);

        return "CommissionVoucher-{$safeNumber}.pdf";
    }
}
