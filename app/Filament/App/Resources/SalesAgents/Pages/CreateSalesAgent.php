<?php

namespace App\Filament\App\Resources\SalesAgents\Pages;

use App\Filament\App\Resources\SalesAgents\SalesAgentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesAgent extends CreateRecord
{
    protected static string $resource = SalesAgentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
