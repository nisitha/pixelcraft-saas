<?php

namespace App\Filament\SuperAdmin\Resources\SubscriptionPayments\Pages;

use App\Filament\SuperAdmin\Resources\SubscriptionPayments\SubscriptionPaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionPayments extends ListRecords
{
    protected static string $resource = SubscriptionPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
