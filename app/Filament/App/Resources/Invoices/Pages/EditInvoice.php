<?php

namespace App\Filament\App\Resources\Invoices\Pages;

use App\Filament\App\Concerns\HasZohoDocumentEditor;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditInvoice extends EditRecord
{
    use HasZohoDocumentEditor;

    protected static string $resource = InvoiceResource::class;

    public function mount(int|string $record): void
    {
        $this->documentType = 'invoice';
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
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->color('primary')
                ->action(function () {
                    /** @var Invoice $record */
                    $record = $this->record;

                    $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                    $filename = "Invoice-{$safeNumber}.pdf";

                    return response()->streamDownload(
                        fn () => print (app(PdfService::class)->generateInvoicePdf($record)->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),

            DeleteAction::make(),
        ];
    }
}
