<?php

namespace App\Filament\App\Resources\SalaryAdvances\Pages;

use App\Filament\App\Resources\SalaryAdvances\SalaryAdvanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalaryAdvance extends EditRecord
{
    protected static string $resource = SalaryAdvanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
