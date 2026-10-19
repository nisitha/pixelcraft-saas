<?php

namespace App\Filament\App\Resources\CommissionPayouts\Pages;

use App\Filament\App\Resources\CommissionPayouts\CommissionPayoutResource;
use App\Models\CommissionPayout;
use App\Models\Invoice;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCommissionPayout extends EditRecord
{
    protected static string $resource = CommissionPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download Voucher PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->color('primary')
                ->action(function () {
                    /** @var CommissionPayout $record */
                    $record = $this->record;
                    $filename = PdfService::getCommissionVoucherFileName($record);

                    return response()->streamDownload(
                        fn () => print (app(PdfService::class)->generateCommissionVoucherPdf($record)->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),

            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->invoice_id && $this->record->status === 'paid') {
            Invoice::where('id', $this->record->invoice_id)->update(['commission_status' => 'paid']);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
