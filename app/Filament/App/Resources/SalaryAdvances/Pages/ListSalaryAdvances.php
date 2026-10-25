<?php

namespace App\Filament\App\Resources\SalaryAdvances\Pages;

use App\Filament\App\Resources\SalaryAdvances\SalaryAdvanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalaryAdvances extends ListRecords
{
    protected static string $resource = SalaryAdvanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
