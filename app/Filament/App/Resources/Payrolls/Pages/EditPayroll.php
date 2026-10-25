<?php

namespace App\Filament\App\Resources\Payrolls\Pages;

use App\Filament\App\Resources\Payrolls\PayrollResource;
use App\Services\PayrollCalculationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayroll extends EditRecord
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_all_payslips')
                ->label('Export All Payslips (Combined PDF)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn (): string => route('payroll.payslips.all', $this->record))
                ->openUrlInNewTab(),

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

            Action::make('mark_as_paid')
                ->label('Mark as Paid & Record Expense')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->visible(fn (): bool => ! $this->record->isPaid())
                ->form([
                    DatePicker::make('payment_date')
                        ->label('Disbursement / Payment Date')
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
                ->action(function (array $data): void {
                    app(PayrollCalculationService::class)->markAsPaidAndRecordExpense($this->record, $data, auth()->id());

                    Notification::make()
                        ->title('Payroll marked as Paid & automated expense logged')
                        ->body('Total Employer Cost booked to company expense ledger.')
                        ->success()
                        ->send();

                    $this->fillForm();
                }),

            DeleteAction::make()
                ->visible(fn (): bool => $this->record->isDraft()),
        ];
    }
}
