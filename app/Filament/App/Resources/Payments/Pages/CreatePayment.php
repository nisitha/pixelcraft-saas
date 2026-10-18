<?php

namespace App\Filament\App\Resources\Payments\Pages;

use App\Filament\App\Resources\Payments\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
}
