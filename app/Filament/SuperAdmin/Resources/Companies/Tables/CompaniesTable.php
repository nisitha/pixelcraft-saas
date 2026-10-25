<?php

namespace App\Filament\SuperAdmin\Resources\Companies\Tables;

use App\Models\Company;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('br_number')
                    ->label('BR No')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('owner_name')
                    ->label('Owner Name')
                    ->state(fn (Company $record): string => $record->users()->wherePivot('role', 'owner')->first()?->name ?? '—')
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('users', function ($q) use ($search) {
                            $q->where('company_user.role', 'owner')->where('users.name', 'like', "%{$search}%");
                        });
                    }),

                TextColumn::make('employees_count')
                    ->label('Employees')
                    ->counts('employees')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('invoices_count')
                    ->label('Invoices')
                    ->counts('invoices')
                    ->sortable()
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('currency')
                    ->label('Currency')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('toggleStatus')
                    ->label(fn (Company $record): string => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (Company $record) => $record->is_active ? Heroicon::OutlinedXCircle : Heroicon::OutlinedCheckCircle)
                    ->color(fn (Company $record): string => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Company $record): void {
                        $record->update(['is_active' => ! $record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Company Activated' : 'Company Deactivated')
                            ->success()
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
