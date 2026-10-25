<?php

namespace App\Filament\App\Resources\Payrolls\RelationManagers;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Services\PayrollCalculationService;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Employee Payroll Breakdown & Salary Slips';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    TextInput::make('basic_salary')
                        ->label('Basic Salary')
                        ->numeric()
                        ->prefix('Rs.')
                        ->required(),

                    TextInput::make('fixed_allowance')
                        ->label('Fixed Allowance')
                        ->numeric()
                        ->prefix('Rs.')
                        ->default(0.00),

                    TextInput::make('additional_allowance')
                        ->label('Additional Allowance (Site OT / Bonus)')
                        ->numeric()
                        ->prefix('Rs.')
                        ->default(0.00)
                        ->helperText('Extra site allowance, performance bonus, or overtime pay'),

                    TextInput::make('advance_deduction')
                        ->label('Salary Advance Deduction')
                        ->numeric()
                        ->prefix('Rs.')
                        ->default(0.00),

                    TextInput::make('other_deductions')
                        ->label('Other Deductions (Penalties / Loans)')
                        ->numeric()
                        ->prefix('Rs.')
                        ->default(0.00),

                    Textarea::make('notes')
                        ->label('Notes / Adjustments Explanation')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        /** @var Payroll $payroll */
        $payroll = $this->getOwnerRecord();
        $isDraft = $payroll->isDraft();

        return $table
            ->recordTitleAttribute('employee.name')
            ->columns([
                TextColumn::make('employee.name')
                    ->label('Employee')
                    ->description(fn (PayrollItem $record): ?string => $record->employee?->designation)
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('basic_salary')
                    ->label('Basic (LKR)')
                    ->money('LKR')
                    ->sortable(),

                TextColumn::make('allowances')
                    ->label('Allowances (LKR)')
                    ->state(fn (PayrollItem $record): float => (float) $record->fixed_allowance + (float) $record->additional_allowance)
                    ->money('LKR'),

                TextColumn::make('gross_salary')
                    ->label('Gross Pay (LKR)')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('epf_employee')
                    ->label('EPF 8% (LKR)')
                    ->money('LKR')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('advance_deduction')
                    ->label('Advance Ded. (LKR)')
                    ->money('LKR')
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'gray'),

                TextColumn::make('other_deductions')
                    ->label('Other Ded. (LKR)')
                    ->money('LKR')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_deductions')
                    ->label('Total Ded. (LKR)')
                    ->money('LKR'),

                TextColumn::make('net_salary')
                    ->label('Net Salary (LKR)')
                    ->money('LKR')
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible($isDraft)
                    ->after(function (PayrollItem $record) use ($payroll): void {
                        app(PayrollCalculationService::class)->recalculateTotals($payroll);
                        Notification::make()
                            ->title('Employee salary item updated and totals recalculated')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
