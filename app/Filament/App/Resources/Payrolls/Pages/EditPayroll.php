<?php

namespace App\Filament\App\Resources\Payrolls\Pages;

use App\Filament\App\Resources\Payrolls\PayrollResource;
use App\Services\PayrollCalculationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm_payroll')
                ->label('Confirm & Finalize Payroll')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn (): bool => $this->record->isDraft())
                ->requiresConfirmation('Are you sure you want to finalize this payroll run? All linked approved salary advances for this month will be marked as deducted.')
                ->action(function (): void {
                    app(PayrollCalculationService::class)->confirmPayroll($this->record, auth()->id());

                    Notification::make()
                        ->title('Payroll run confirmed & finalized successfully')
                        ->body('All employee salary advances have been marked as deducted.')
                        ->success()
                        ->send();

                    $this->fillForm();
                }),

            DeleteAction::make()
                ->visible(fn (): bool => $this->record->isDraft()),
        ];
    }
}
