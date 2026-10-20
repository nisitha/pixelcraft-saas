<?php

namespace App\Filament\App\Resources\CompletionCertificates\Pages;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Models\CompletionCertificate;
use App\Models\Contact;
use App\Services\PdfService;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCompletionCertificate extends CreateRecord
{
    protected static string $resource = CompletionCertificateResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function downloadPdf()
    {
        $data = $this->form->getState();
        $cert = new CompletionCertificate($data);
        $company = Filament::getTenant();

        if ($company) {
            $cert->company_id = $company->id;
            $cert->setRelation('company', $company);
        }

        if (! empty($cert->contact_id)) {
            $cert->setRelation('contact', Contact::find($cert->contact_id));
        }

        $cert->certificate_number = $cert->certificate_number ?: 'DRAFT-CERT';
        $filename = 'Draft-'.PdfService::sanitizeFileName($cert->certificate_number).'.pdf';

        return response()->streamDownload(
            fn () => print (app(PdfService::class)->generateCompletionCertificatePdf($cert)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
