<?php

namespace App\Filament\App\Resources\Quotations\Pages;

use App\Filament\App\Concerns\HasZohoDocumentEditor;
use App\Filament\App\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    use HasZohoDocumentEditor;

    protected static string $resource = QuotationResource::class;

    public function mount(): void
    {
        $this->documentType = 'quotation';
        parent::mount();
        $this->initZohoDocument();
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
