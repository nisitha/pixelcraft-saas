<?php

namespace App\Filament\App\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('expense_date', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Expense / Payee')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color('primary'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->label('Expense Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('reference_no')
                    ->label('Reference / Bill #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice.invoice_number')
                    ->label('Linked Invoice')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('General Overhead'),
                IconColumn::make('is_internal_overhead')
                    ->label('Internal Overhead')
                    ->boolean()
                    ->trueIcon('heroicon-o-building-office-2')
                    ->falseIcon('heroicon-o-arrow-up-right')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_internal_overhead')
                    ->label('Overhead Type')
                    ->placeholder('All Expenses')
                    ->trueLabel('Internal Office Overhead')
                    ->falseLabel('External Third-Party Outflow'),
                Filter::make('expense_date')
                    ->form([
                        DatePicker::make('from')->label('Expense Date From'),
                        DatePicker::make('until')->label('Expense Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('expense_date', '<=', $date),
                            );
                    }),

                SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'Equipment & Hardware' => 'Equipment & Hardware',
                        'Material & Cables' => 'Material & Cables',
                        'Installation Labour' => 'Installation Labour',
                        'Site Food & Travel' => 'Site Food & Travel',
                        'Office & Utilities' => 'Office & Utilities',
                        'Salaries & Allowances' => 'Salaries & Allowances',
                        'Marketing & Sales' => 'Marketing & Sales',
                        'General Overhead' => 'General Overhead',
                    ]),

                SelectFilter::make('invoice_id')
                    ->label('Filter by Project / Invoice')
                    ->relationship('invoice', 'invoice_number')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
                ]),
            ]);
    }
}
