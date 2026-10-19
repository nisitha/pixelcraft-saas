<?php

namespace App\Filament\App\Resources\SalesAgents\Pages;

use App\Filament\App\Resources\SalesAgents\SalesAgentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalesAgents extends ListRecords
{
    protected static string $resource = SalesAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Sales Agent'),
        ];
    }
}
