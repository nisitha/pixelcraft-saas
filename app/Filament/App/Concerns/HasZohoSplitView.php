<?php

namespace App\Filament\App\Concerns;

use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quotation;
use App\Services\PdfService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HasZohoSplitView
{
    public string $documentType = 'quotation'; // 'quotation' or 'invoice'

    public string $search = '';

    public string $statusFilter = 'all';

    // Email Modal State
    public bool $showEmailModal = false;

    public string $emailRecipient = '';

    public string $emailSubject = '';

    public string $emailMessage = '';

    // Payment Modal State
    public bool $showPaymentModal = false;

    public float $paymentAmount = 0.00;

    public string $paymentMethod = 'bank_transfer';

    public string $paymentReference = '';

    public string $paymentNotes = '';

    public ?string $previewMode = null;

    public ?string $previewSummaryMode = null;

    public function initSplitView(): void
    {
        if ($this->record && $this->documentType === 'invoice' && method_exists($this->record, 'expenses')) {
            $this->record->loadMissing(['expenses']);
        }
        $this->previewMode = $this->record?->price_display_mode ?? 'itemized';
        $this->previewSummaryMode = $this->record?->pdf_item_summary_mode ?? 'consolidated';
        $this->refreshEmailDetails();
    }

    public function getRecordsListProperty(): Collection
    {
        $companyId = Filament::getTenant()?->id;

        $query = static::getModel()::where('company_id', $companyId)
            ->with(['contact'])
            ->latest();

        if (! empty($this->search)) {
            $search = '%'.$this->search.'%';
            $numField = $this->documentType === 'quotation' ? 'quote_number' : 'invoice_number';

            $query->where(function ($q) use ($search, $numField) {
                $q->where($numField, 'like', $search)
                    ->orWhereHas('contact', fn ($cq) => $cq->where('name', 'like', $search));
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->get();
    }

    public function selectRecord(int $id): void
    {
        $companyId = Filament::getTenant()?->id;

        $relations = ['contact', 'items.product.category'];
        if ($this->documentType === 'invoice') {
            $relations[] = 'expenses';
        }

        $record = static::getModel()::where('company_id', $companyId)
            ->with($relations)
            ->find($id);

        if ($record) {
            $this->record = $record;
            $this->previewMode = $record->price_display_mode ?? 'itemized';
            $this->previewSummaryMode = $record->pdf_item_summary_mode ?? 'consolidated';
            $this->refreshEmailDetails();
        }
    }

    protected function refreshEmailDetails(): void
    {
        if (! $this->record) {
            return;
        }

        $docNum = $this->documentType === 'quotation' ? $this->record->quote_number : $this->record->invoice_number;
        $tenant = Filament::getTenant();
        $companyName = $tenant?->name ?? config('app.name', 'PixelCraft SaaS');
        $currency = $tenant?->currency ?? 'LKR';
        $customerName = $this->record->contact?->name ?? 'Valued Customer';

        $this->emailRecipient = $this->record->contact?->email ?? '';
        $this->emailSubject = ucfirst($this->documentType)." #{$docNum} from {$companyName}";
        $this->emailMessage = "Dear {$customerName},\n\nPlease find your official {$this->documentType} #{$docNum} attached.\n\nTotal Amount: {$currency} ".number_format($this->record->grand_total, 2)."\n\nYou can also view your verified document online:\n{$this->getPublicShareUrlProperty()}\n\nThank you for choosing {$companyName}!";
    }

    public function markAsSent(): void
    {
        if (! $this->record) {
            return;
        }

        $data = [
            'is_sent' => true,
            'sent_at' => now(),
        ];

        if ($this->documentType === 'quotation' && $this->record->status === 'draft') {
            $data['status'] = 'sent';
        }

        $this->record->update($data);

        Notification::make()
            ->title(ucfirst($this->documentType).' marked as Sent')
            ->success()
            ->send();
    }

    public function unlockForEditing()
    {
        if (! $this->record) {
            return null;
        }

        $this->record->update(['is_sent' => false]);

        Notification::make()
            ->title('Document unlocked for editing')
            ->info()
            ->send();

        return redirect()->to($this->getResource()::getUrl('edit', ['record' => $this->record->id]));
    }

    public function openEmailModal(): void
    {
        $this->refreshEmailDetails();
        $this->showEmailModal = true;
    }

    public function closeEmailModal(): void
    {
        $this->showEmailModal = false;
    }

    public function sendEmail(): void
    {
        $this->validate([
            'emailRecipient' => 'required|email',
            'emailSubject' => 'required|string',
        ]);

        $this->markAsSent();
        $this->showEmailModal = false;

        Notification::make()
            ->title("Document dispatched to {$this->emailRecipient}")
            ->success()
            ->body('Document has been sent and status updated to Sent.')
            ->send();
    }

    public function getWhatsAppUrlProperty(): string
    {
        if (! $this->record) {
            return '#';
        }

        $rawPhone = $this->record->contact?->phone ?? '';
        $phone = preg_replace('/[^0-9]/', '', $rawPhone);

        if (str_starts_with($phone, '0')) {
            $phone = '94'.substr($phone, 1);
        } elseif (! str_starts_with($phone, '94') && strlen($phone) === 9) {
            $phone = '94'.$phone;
        }

        $docNum = $this->documentType === 'quotation' ? $this->record->quote_number : $this->record->invoice_number;
        $tenant = Filament::getTenant();
        $companyName = $tenant?->name ?? config('app.name', 'PixelCraft SaaS');
        $currency = $tenant?->currency ?? 'LKR';
        $custName = $this->record->contact?->name ?? 'Valued Customer';
        $amount = number_format($this->record->grand_total, 2);
        $link = $this->getPublicShareUrlProperty();

        $message = "Hello {$custName},\n\nHere is your official {$this->documentType} #{$docNum} from {$companyName}.\nTotal Amount: {$currency} {$amount}\n\nYou can view and download your official document online here:\n{$link}\n\nThank you for choosing {$companyName}!";

        return "https://wa.me/{$phone}?text=".rawurlencode($message);
    }

    public function getPublicShareUrlProperty(): string
    {
        if (! $this->record) {
            return '#';
        }

        return URL::signedRoute('public.document.show', [
            'type' => $this->documentType,
            'id' => $this->record->id,
        ]);
    }

    public function getEffectivePriceDisplayModeProperty(): string
    {
        return $this->previewMode ?? ($this->record?->price_display_mode ?? 'itemized');
    }

    public function getEffectivePdfItemSummaryModeProperty(): string
    {
        return $this->previewSummaryMode ?? ($this->record?->pdf_item_summary_mode ?? 'consolidated');
    }

    public function togglePreviewPriceDisplayMode(): void
    {
        $current = $this->getEffectivePriceDisplayModeProperty();
        $newMode = $current === 'package_total_only' ? 'itemized' : 'package_total_only';
        $this->previewMode = $newMode;

        if ($this->record) {
            $this->record->update(['price_display_mode' => $newMode]);
        }

        Notification::make()
            ->title('Preview Pricing: '.($newMode === 'package_total_only' ? 'Customer Package View (Hidden Unit Prices)' : 'Standard Itemized View (With Prices)'))
            ->success()
            ->send();
    }

    public function togglePreviewPdfItemSummaryMode(): void
    {
        $current = $this->getEffectivePdfItemSummaryModeProperty();
        $newMode = $current === 'consolidated' ? 'detailed' : 'consolidated';
        $this->previewSummaryMode = $newMode;

        if ($this->record) {
            $this->record->update(['pdf_item_summary_mode' => $newMode]);
        }

        Notification::make()
            ->title('Scope Layout: '.($newMode === 'consolidated' ? 'Consolidated Summary (Strict 1-Page PDF)' : 'Detailed Breakdown (All Items)'))
            ->success()
            ->send();
    }

    public function downloadCustomerPdf(): StreamedResponse
    {
        $pdfService = app(PdfService::class);
        $summaryMode = $this->getEffectivePdfItemSummaryModeProperty();

        if ($this->documentType === 'quotation') {
            /** @var Quotation $record */
            $record = $this->record;
            $safeNumber = str_replace(['/', '\\'], '-', $record->quote_number);
            $filename = "Quotation-Customer-{$safeNumber}.pdf";

            return response()->streamDownload(
                fn () => print ($pdfService->generateQuotationPdf($record, 'package_total_only', $summaryMode)->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        }

        /** @var Invoice $record */
        $record = $this->record;
        $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
        $filename = "Invoice-Customer-{$safeNumber}.pdf";

        return response()->streamDownload(
            fn () => print ($pdfService->generateInvoicePdf($record, 'package_total_only', $summaryMode)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadItemizedPdf(): StreamedResponse
    {
        $pdfService = app(PdfService::class);
        $summaryMode = $this->getEffectivePdfItemSummaryModeProperty();

        if ($this->documentType === 'quotation') {
            /** @var Quotation $record */
            $record = $this->record;
            $safeNumber = str_replace(['/', '\\'], '-', $record->quote_number);
            $filename = "Quotation-Itemized-{$safeNumber}.pdf";

            return response()->streamDownload(
                fn () => print ($pdfService->generateQuotationPdf($record, 'itemized', $summaryMode)->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        }

        /** @var Invoice $record */
        $record = $this->record;
        $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
        $filename = "Invoice-Itemized-{$safeNumber}.pdf";

        return response()->streamDownload(
            fn () => print ($pdfService->generateInvoicePdf($record, 'itemized', $summaryMode)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadPdf(): StreamedResponse
    {
        $mode = $this->getEffectivePriceDisplayModeProperty();

        return $mode === 'package_total_only'
            ? $this->downloadCustomerPdf()
            : $this->downloadItemizedPdf();
    }

    public function downloadSolarAgreement(): StreamedResponse
    {
        $pdfService = app(PdfService::class);
        $record = $this->record;
        $filename = PdfService::getSolarAgreementFileName($record);

        return response()->streamDownload(
            fn () => print ($pdfService->generateSolarAgreementPdf($record)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadDeliveryNote(): StreamedResponse
    {
        $pdfService = app(PdfService::class);
        $filename = PdfService::getDeliveryNoteFileName($this->record);

        return response()->streamDownload(
            fn () => print ($pdfService->generateDeliveryNotePdf($this->record)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function downloadReceiptVoucher(): ?StreamedResponse
    {
        if ($this->documentType !== 'invoice' || ! $this->record) {
            return null;
        }

        $payment = $this->record->payments()->latest()->first();
        if (! $payment) {
            Notification::make()
                ->title('No payments recorded for this invoice yet.')
                ->warning()
                ->send();

            return null;
        }

        $pdfService = app(PdfService::class);
        $filename = PdfService::getPaymentReceiptFileName($payment);

        return response()->streamDownload(
            fn () => print ($pdfService->generatePaymentReceiptPdf($payment)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function duplicateRecord()
    {
        if (! $this->record) {
            return null;
        }

        $companyId = Filament::getTenant()?->id;

        if ($this->documentType === 'quotation') {
            /** @var Quotation $source */
            $source = $this->record;

            $newQuote = DB::transaction(function () use ($source, $companyId) {
                $newNumber = 'QUO-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

                $quote = Quotation::create([
                    'company_id' => $companyId,
                    'contact_id' => $source->contact_id,
                    'quote_number' => $newNumber,
                    'quote_date' => now()->toDateString(),
                    'valid_until' => now()->addDays(30)->toDateString(),
                    'status' => 'draft',
                    'system_kw' => $source->system_kw,
                    'phase_type' => $source->phase_type,
                    'roof_type' => $source->roof_type,
                    'inverter_brand' => $source->inverter_brand,
                    'panel_brand' => $source->panel_brand,
                    'subtotal' => $source->subtotal,
                    'discount' => $source->discount,
                    'tax' => $source->tax,
                    'profit_markup' => $source->profit_markup ?? 0.00,
                    'profit_markup_type' => $source->profit_markup_type ?? 'fixed',
                    'grand_total' => $source->grand_total,
                    'terms' => $source->terms,
                    'is_sent' => false,
                ]);

                foreach ($source->items as $item) {
                    $quote->items()->create([
                        'product_id' => $item->product_id,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'cost_price' => $item->cost_price ?? ($item->product?->cost_price ?? 0.00),
                        'unit_price' => $item->unit_price,
                        'tax' => $item->tax ?? 0.00,
                        'subtotal' => $item->subtotal,
                    ]);
                }

                return $quote;
            });

            Notification::make()
                ->title("Quotation duplicated as #{$newQuote->quote_number}")
                ->success()
                ->send();

            $this->selectRecord($newQuote->id);

            return null;
        } else {
            /** @var Invoice $source */
            $source = $this->record;

            $newInvoice = DB::transaction(function () use ($source, $companyId) {
                $newNumber = 'INV-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);

                $invoice = Invoice::create([
                    'company_id' => $companyId,
                    'contact_id' => $source->contact_id,
                    'quotation_id' => $source->quotation_id,
                    'invoice_number' => $newNumber,
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->addDays(14)->toDateString(),
                    'type' => $source->type,
                    'subtotal' => $source->subtotal,
                    'discount' => $source->discount,
                    'tax' => $source->tax,
                    'profit_markup' => $source->profit_markup ?? 0.00,
                    'profit_markup_type' => $source->profit_markup_type ?? 'fixed',
                    'grand_total' => $source->grand_total,
                    'paid_amount' => 0.00,
                    'due_amount' => $source->grand_total,
                    'status' => 'unpaid',
                    'terms' => $source->terms,
                    'is_sent' => false,
                ]);

                foreach ($source->items as $item) {
                    $invoice->items()->create([
                        'product_id' => $item->product_id,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'cost_price' => $item->cost_price ?? ($item->product?->cost_price ?? 0.00),
                        'unit_price' => $item->unit_price,
                        'tax' => $item->tax ?? 0.00,
                        'subtotal' => $item->subtotal,
                    ]);
                }

                return $invoice;
            });

            Notification::make()
                ->title("Invoice duplicated as #{$newInvoice->invoice_number}")
                ->success()
                ->send();

            $this->selectRecord($newInvoice->id);

            return null;
        }
    }

    public function convertToInvoice()
    {
        if ($this->documentType !== 'quotation' || ! $this->record) {
            return null;
        }

        /** @var Quotation $record */
        $record = $this->record;

        $invoice = DB::transaction(function () use ($record) {
            $invoice = Invoice::create([
                'company_id' => $record->company_id,
                'contact_id' => $record->contact_id,
                'quotation_id' => $record->id,
                'invoice_number' => 'INV-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'type' => 'milestone',
                'subtotal' => $record->subtotal,
                'discount' => $record->discount,
                'tax' => $record->tax,
                'profit_markup' => $record->profit_markup ?? 0.00,
                'profit_markup_type' => $record->profit_markup_type ?? 'fixed',
                'grand_total' => $record->grand_total,
                'paid_amount' => 0.00,
                'due_amount' => $record->grand_total,
                'price_display_mode' => $record->price_display_mode ?? 'itemized',
                'status' => 'unpaid',
            ]);

            foreach ($record->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'warranty_text' => $item->warranty_text,
                    'quantity' => $item->quantity,
                    'cost_price' => $item->cost_price ?? ($item->product?->cost_price ?? 0.00),
                    'unit_price' => $item->unit_price,
                    'tax' => $item->tax ?? 0.00,
                    'subtotal' => $item->subtotal,
                    'hide_on_customer_pdf' => (bool) $item->hide_on_customer_pdf,
                ]);
            }

            $record->update(['status' => 'invoiced']);

            return $invoice;
        });

        Notification::make()
            ->title('Quotation converted to Invoice #'.$invoice->invoice_number)
            ->success()
            ->send();

        return redirect()->to(InvoiceResource::getUrl('view', ['record' => $invoice->id]));
    }

    public function openPaymentModal(): void
    {
        if (! $this->record || $this->documentType !== 'invoice') {
            return;
        }

        $this->paymentAmount = (float) $this->record->due_amount;
        $this->paymentMethod = 'bank_transfer';
        $this->paymentReference = 'REF-'.rand(1000, 9999);
        $this->paymentNotes = 'Payment received for Invoice #'.$this->record->invoice_number;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
    }

    public function submitPayment(): void
    {
        if (! $this->record || $this->documentType !== 'invoice') {
            return;
        }

        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|string',
        ]);

        /** @var Invoice $invoice */
        $invoice = $this->record;

        DB::transaction(function () use ($invoice) {
            Payment::create([
                'company_id' => $invoice->company_id,
                'invoice_id' => $invoice->id,
                'payment_number' => 'PAY-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'amount' => $this->paymentAmount,
                'payment_date' => now()->toDateString(),
                'payment_method' => $this->paymentMethod,
                'reference_number' => $this->paymentReference,
                'notes' => $this->paymentNotes,
            ]);

            $newPaid = (float) $invoice->paid_amount + $this->paymentAmount;
            $newDue = max(0, (float) $invoice->grand_total - $newPaid);
            $newStatus = $newDue <= 0.01 ? 'paid' : 'partial';

            $invoice->update([
                'paid_amount' => $newPaid,
                'due_amount' => $newDue,
                'status' => $newStatus,
            ]);
        });

        $this->record = $invoice->fresh(['items.product', 'contact', 'payments']);
        $this->showPaymentModal = false;

        Notification::make()
            ->title('Payment of Rs. '.number_format($this->paymentAmount, 2).' recorded successfully')
            ->success()
            ->send();
    }
}
