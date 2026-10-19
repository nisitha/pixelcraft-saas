<?php

namespace App\Filament\App\Resources\CommissionPayouts\Pages;

use App\Filament\App\Resources\CommissionPayouts\CommissionPayoutResource;
use App\Models\Invoice;
use Filament\Resources\Pages\CreateRecord;

class CreateCommissionPayout extends CreateRecord
{
    protected static string $resource = CommissionPayoutResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->invoice_id && $this->record->status === 'paid') {
            Invoice::where('id', $this->record->invoice_id)->update(['commission_status' => 'paid']);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
