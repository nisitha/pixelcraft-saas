<?php

namespace App\Filament\App\Resources\Quotations\Pages;

use App\Filament\App\Resources\Quotations\QuotationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
