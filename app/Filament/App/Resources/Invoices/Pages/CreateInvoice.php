<?php

namespace App\Filament\App\Resources\Invoices\Pages;

use App\Filament\App\Concerns\HasZohoDocumentEditor;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    use HasZohoDocumentEditor;

    protected static string $resource = InvoiceResource::class;

    public function mount(): void
    {
        $this->documentType = 'invoice';
        parent::mount();
        $this->initZohoDocument();
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
