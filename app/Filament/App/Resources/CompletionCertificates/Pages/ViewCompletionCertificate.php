<?php

namespace App\Filament\App\Resources\CompletionCertificates\Pages;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Models\CompletionCertificate;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewCompletionCertificate extends ViewRecord
{
    protected static string $resource = CompletionCertificateResource::class;

    protected string $view = 'filament.app.resources.completion-certificates.view-certificate';

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

            EditAction::make(),
        ];
    }
}
