<?php

namespace App\Filament\App\Resources\Quotations\Pages;

use App\Filament\App\Concerns\HasZohoSplitView;
use App\Filament\App\Resources\Quotations\QuotationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewQuotation extends ViewRecord
{
    use HasZohoSplitView;

    protected static string $resource = QuotationResource::class;

    protected string $view = 'filament.app.pages.zoho-split-view';

    public function mount(int|string $record): void
    {
        $this->documentType = 'quotation';
        parent::mount($record);
        $this->initSplitView();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
