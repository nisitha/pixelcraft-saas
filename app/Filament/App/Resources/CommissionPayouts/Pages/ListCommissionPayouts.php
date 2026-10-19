<?php

namespace App\Filament\App\Resources\CommissionPayouts\Pages;

use App\Filament\App\Resources\CommissionPayouts\CommissionPayoutResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommissionPayouts extends ListRecords
{
    protected static string $resource = CommissionPayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Commission Payout'),
        ];
    }
}
