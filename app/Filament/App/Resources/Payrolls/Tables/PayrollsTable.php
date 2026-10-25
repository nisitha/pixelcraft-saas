<?php

namespace App\Filament\App\Resources\Payrolls\Tables;

use App\Filament\App\Resources\Payrolls\PayrollResource;
use App\Models\Payroll;
use App\Services\PayrollCalculationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PayrollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Payroll $record): string => PayrollResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('month')
                    ->label('Month')
                    ->badge()
                    ->color('gray')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Run Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_gross')
                    ->label('Total Gross (LKR)')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('total_epf_etf')
                    ->label('Total EPF/ETF (LKR)')
                    ->state(fn (Payroll $record): float => $record->total_epf_etf)
                    ->money('LKR')
                    ->sortable(),

                TextColumn::make('total_advances')
                    ->label('Advances Deducted (LKR)')
                    ->money('LKR')
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),

                TextColumn::make('total_net_payable')
                    ->label('Total Net Salary (LKR)')
                    ->money('LKR')
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Employees')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'confirmed' => 'success',
                        'paid' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('processor.name')
                    ->label('Processed By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'paid' => 'Paid',
                    ]),
            ])
            ->recordActions([
                Action::make('export_all_payslips')
                    ->label('Export All')
                    ->tooltip('Export All Payslips (Combined PDF)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Payroll $record): string => route('payroll.payslips.all', $record))
                    ->openUrlInNewTab(),

                Action::make('confirm')
                    ->label('Confirm & Finalize')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Payroll $record): bool => $record->isDraft())
                    ->requiresConfirmation('Are you sure you want to finalize this payroll run? All linked salary advances will be marked as deducted.')
                    ->action(function (Payroll $record): void {
                        app(PayrollCalculationService::class)->confirmPayroll($record, auth()->id());
                        Notification::make()
                            ->title('Payroll confirmed successfully! Advances marked as deducted.')
                            ->success()
                            ->send();
                    }),

                Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->tooltip('Mark as Paid & Record Expense')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->visible(fn (Payroll $record): bool => ! $record->isPaid())
                    ->form([
                        DatePicker::make('payment_date')
                            ->label('Disbursement Date')
                            ->default(now())
                            ->required(),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer (Direct Remittance)',
                                'cheque' => 'Corporate Cheque',
                                'cash' => 'Cash Voucher',
                            ])
                            ->default('bank_transfer')
                            ->required(),

                        Select::make('category')
                            ->label('Expense Category')
                            ->options([
                                'Salaries & Wages' => 'Salaries & Wages',
                                'Staff Payroll' => 'Staff Payroll',
                                'Staff Allowances' => 'Staff Allowances',
                            ])
                            ->default('Salaries & Wages')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Ledger Notes')
                            ->placeholder('Optional notes for the company expense transaction')
                            ->rows(2),
                    ])
                    ->action(function (Payroll $record, array $data): void {
                        app(PayrollCalculationService::class)->markAsPaidAndRecordExpense($record, $data, auth()->id());

                        Notification::make()
                            ->title('Payroll marked as Paid & automated expense logged')
                            ->body('Total Employer Cost booked to company expense ledger.')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
                ]),
            ]);
    }
}
