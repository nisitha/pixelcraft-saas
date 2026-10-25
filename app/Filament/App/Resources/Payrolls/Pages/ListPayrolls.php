<?php

namespace App\Filament\App\Resources\Payrolls\Pages;

use App\Filament\App\Resources\Payrolls\PayrollResource;
use App\Filament\App\Widgets\PayrollsOverviewWidget;
use App\Models\Payroll;
use App\Services\PayrollCalculationService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPayrolls extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_payroll')
                ->label('Generate Monthly Payroll')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalHeading('Generate Monthly Payroll Run')
                ->modalDescription('Specify the payroll target month (YYYY-MM). Active employees will be automatically calculated with statutory EPF, ETF, and approved advances.')
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

    protected function getHeaderWidgets(): array
    {
        return [
            PayrollsOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Payroll Runs')
                ->badge(fn () => Payroll::count()),

            'draft' => Tab::make('Drafts')
                ->badge(fn () => Payroll::where('status', 'draft')->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),

            'confirmed' => Tab::make('Confirmed')
                ->badge(fn () => Payroll::where('status', 'confirmed')->count() ?: null)
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed')),

            'paid' => Tab::make('Paid & Settled')
                ->badge(fn () => Payroll::where('status', 'paid')->count() ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid')),
        ];
    }
}
