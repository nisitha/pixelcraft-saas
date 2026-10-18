<?php

namespace App\Filament\App\Resources\ProductSerials\Pages;

use App\Filament\App\Resources\ProductSerials\ProductSerialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductSerial extends EditRecord
{
    protected static string $resource = ProductSerialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
