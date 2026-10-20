<?php

namespace App\Filament\App\Resources\CompletionCertificates\Pages;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Models\CompletionCertificate;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class EditCompletionCertificate extends EditRecord
{
    protected static string $resource = CompletionCertificateResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function downloadPdf()
    {
        /** @var CompletionCertificate $record */
        $record = $this->record;

        // Populate with current form state to reflect in-flight changes
        $data = $this->form->getState();
        $record->fill($data);

        $filename = PdfService::getCompletionCertificateFileName($record);

        return response()->streamDownload(
            fn () => print (app(PdfService::class)->generateCompletionCertificatePdf($record)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->color('primary')
                ->action(fn () => $this->downloadPdf()),

            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
