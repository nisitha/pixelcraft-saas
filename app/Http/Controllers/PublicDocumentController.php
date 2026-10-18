<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicDocumentController extends Controller
{
    public function show(Request $request, string $type, int|string $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This document preview link has expired or is invalid.');
        }

        if ($type === 'quotation') {
            $document = Quotation::with(['company', 'contact', 'items.product'])->findOrFail($id);
        } elseif ($type === 'invoice') {
            $document = Invoice::with(['company', 'contact', 'items.product', 'payments'])->findOrFail($id);
        } else {
            abort(404, 'Invalid document type.');
        }

        return view('public.document-preview', [
            'document' => $document,
            'type' => $type,
            'company' => $document->company,
            'contact' => $document->contact,
            'items' => $document->items,
        ]);
    }

    public function download(Request $request, string $type, int|string $id): StreamedResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This download link has expired or is invalid.');
        }

        $pdfService = app(PdfService::class);

        if ($type === 'quotation') {
            $quotation = Quotation::with(['company', 'contact', 'items.product'])->findOrFail($id);
            $filename = "Quotation-{$quotation->quote_number}.pdf";

            return response()->streamDownload(
                fn () => print ($pdfService->generateQuotationPdf($quotation)->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        }

        if ($type === 'invoice') {
            $invoice = Invoice::with(['company', 'contact', 'items.product', 'payments'])->findOrFail($id);
            $filename = "Invoice-{$invoice->invoice_number}.pdf";

            return response()->streamDownload(
                fn () => print ($pdfService->generateInvoicePdf($invoice)->output()),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        }

        abort(404, 'Invalid document type.');
    }
}
