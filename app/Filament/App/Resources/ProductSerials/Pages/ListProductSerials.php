<?php

namespace App\Filament\App\Resources\ProductSerials\Pages;

use App\Filament\App\Resources\ProductSerials\ProductSerialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductSerials extends ListRecords
{
    protected static string $resource = ProductSerialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
