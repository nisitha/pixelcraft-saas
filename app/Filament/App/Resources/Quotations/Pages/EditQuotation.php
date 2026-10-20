<?php

namespace App\Filament\App\Resources\Quotations\Pages;

use App\Filament\App\Concerns\HasZohoDocumentEditor;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Filament\App\Resources\Quotations\QuotationResource;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class EditQuotation extends EditRecord
{
    use HasZohoDocumentEditor;

    protected static string $resource = QuotationResource::class;

    public function mount(int|string $record): void
    {
        $this->documentType = 'quotation';
        parent::mount($record);
        $this->initZohoDocument();
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('convertToInvoice')
                ->label('Convert to Invoice')
                ->icon(Heroicon::ArrowPathRoundedSquare)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Convert Quotation to Invoice')
                ->modalDescription('Are you sure you want to convert this quotation into an invoice? All line items and pricing will be transferred.')
                ->visible(fn (): bool => in_array($this->record->status, ['accepted', 'sent', 'draft']))
                ->action(function (Action $action) {
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
                            'grand_total' => $record->grand_total,
                            'paid_amount' => 0.00,
                            'due_amount' => $record->grand_total,
                            'status' => 'unpaid',
                        ]);

                        foreach ($record->items as $item) {
                            $invoice->items()->create([
                                'product_id' => $item->product_id,
                                'description' => $item->description,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'tax' => $item->tax ?? 0.00,
                                'subtotal' => $item->subtotal,
                            ]);
                        }

                        $record->update(['status' => 'invoiced']);

                        return $invoice;
                    });

                    Notification::make()
                        ->title('Quotation converted to Invoice #'.$invoice->invoice_number)
                        ->success()
                        ->send();

                    return $action->redirect(InvoiceResource::getUrl('edit', ['record' => $invoice->id]));
                }),

            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->color('primary')
                ->action(function () {
                    /** @var Quotation $record */
                    $record = $this->record;

                    $safeNumber = str_replace(['/', '\\'], '-', $record->quote_number);
                    $filename = "Quotation-{$safeNumber}.pdf";

                    return response()->streamDownload(
                        fn () => print (app(PdfService::class)->generateQuotationPdf($record, 'itemized')->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),

            Action::make('downloadCustomerPdf')
                ->label('Customer Package PDF')
                ->icon(Heroicon::DocumentText)
                ->color('gray')
                ->action(function () {
                    /** @var Quotation $record */
                    $record = $this->record;

                    $safeNumber = str_replace(['/', '\\'], '-', $record->quote_number);
                    $filename = "Quotation-{$safeNumber}-Package.pdf";

                    return response()->streamDownload(
                        fn () => print (app(PdfService::class)->generateQuotationPdf($record, 'package_total_only')->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),

            DeleteAction::make(),
        ];
    }
}
