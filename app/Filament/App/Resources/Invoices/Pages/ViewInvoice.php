<?php

namespace App\Filament\App\Resources\Invoices\Pages;

use App\Filament\App\Concerns\HasZohoSplitView;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    use HasZohoSplitView;

    protected static string $resource = InvoiceResource::class;

    protected string $view = 'filament.app.pages.zoho-split-view';

    public function mount(int|string $record): void
    {
        $this->documentType = 'invoice';
        parent::mount($record);
        $this->initSplitView();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
