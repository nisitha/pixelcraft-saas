<?php

namespace App\Filament\App\Resources\SalaryAdvances\Tables;

use App\Filament\App\Resources\SalaryAdvances\SalaryAdvanceResource;
use App\Models\SalaryAdvance;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SalaryAdvancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (SalaryAdvance $record): string => SalaryAdvanceResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('employee.name')
                    ->label('Employee')
                    ->description(fn (SalaryAdvance $record): ?string => $record->employee?->designation)
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('amount')
                    ->label('Amount (LKR)')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('request_date')
                    ->label('Request Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('deduct_month')
                    ->label('Deduction Month')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'deducted' => 'info',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('approver.name')
                    ->label('Approved By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'deducted' => 'Deducted',
                        'rejected' => 'Rejected',
                    ]),

                SelectFilter::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (SalaryAdvance $record): bool => $record->status === 'pending')
                    ->action(function (SalaryAdvance $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Salary Advance Approved')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (SalaryAdvance $record): bool => $record->status === 'pending')
                    ->action(function (SalaryAdvance $record): void {
                        $record->update([
                            'status' => 'rejected',
                        ]);

                        Notification::make()
                            ->title('Salary Advance Rejected')
                            ->danger()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
