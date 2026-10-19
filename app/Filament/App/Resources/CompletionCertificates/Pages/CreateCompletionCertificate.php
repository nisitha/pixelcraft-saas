<?php

namespace App\Filament\App\Resources\CompletionCertificates\Pages;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompletionCertificate extends CreateRecord
{
    protected static string $resource = CompletionCertificateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
