<?php

namespace App\Filament\App\Resources\CompletionCertificates\Pages;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Models\CompletionCertificate;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCompletionCertificate extends EditRecord
{
    protected static string $resource = CompletionCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->color('primary')
                ->action(function () {
                    /** @var CompletionCertificate $record */
                    $record = $this->record;
                    $filename = PdfService::getCompletionCertificateFileName($record);

                    return response()->streamDownload(
                        fn () => print (app(PdfService::class)->generateCompletionCertificatePdf($record)->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),

            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
