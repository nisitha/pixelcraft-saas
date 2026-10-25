<?php

namespace App\Filament\App\Resources\Payrolls\Pages;

use App\Filament\App\Resources\Payrolls\PayrollResource;
use App\Services\PayrollCalculationService;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePayroll extends CreateRecord
{
    protected static string $resource = PayrollResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = Filament::getTenant();
        $service = app(PayrollCalculationService::class);

        return $service->generateMonthlyPayroll(
            $tenant->id,
            $data['month'],
            auth()->id()
        );
    }
}
