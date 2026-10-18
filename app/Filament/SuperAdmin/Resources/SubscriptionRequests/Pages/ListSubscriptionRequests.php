<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionRequests\Pages;

use App\Filament\SuperAdmin\Resources\SubscriptionRequests\SubscriptionRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionRequests extends ListRecords
{
    protected static string $resource = SubscriptionRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Submissions are made by tenants from their portal
        ];
    }
}
