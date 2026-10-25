<?php

namespace App\Filament\App\Resources\Payrolls\Pages;

use App\Filament\App\Resources\Payrolls\PayrollResource;
use App\Services\PayrollCalculationService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_payroll')
                ->label('Generate Monthly Payroll')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalHeading('Generate Monthly Payroll')
                ->modalDescription('Specify the payroll run month (YYYY-MM). Active employees will be loaded with EPF, ETF, and approved advances calculated.')
                ->modalSubmitActionLabel('Generate Payroll')
                ->schema([
                    TextInput::make('month')
                        ->label('Target Month (YYYY-MM)')
                        ->placeholder('e.g. 2026-10')
                        ->default(now()->format('Y-m'))
                        ->regex('/^\d{4}-\d{2}$/')
                        ->helperText('Format: YYYY-MM (e.g. 2026-10)')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $tenant = Filament::getTenant();
                    $service = app(PayrollCalculationService::class);

                    try {
                        $payroll = $service->generateMonthlyPayroll(
                            $tenant->id,
                            $data['month'],
                            auth()->id()
                        );

                        Notification::make()
                            ->title("Payroll run for {$payroll->month} generated successfully")
                            ->body("Calculated for {$payroll->items()->count()} active employees.")
                            ->success()
                            ->send();

                        $this->redirect(PayrollResource::getUrl('edit', ['record' => $payroll]));
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Failed to generate payroll')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
