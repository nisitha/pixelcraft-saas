<?php

namespace App\Filament\App\Resources\Employees\Tables;

use App\Filament\App\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Employee $record): string => EmployeeResource::getUrl('edit', ['record' => $record]))
            ->columns([
                TextColumn::make('name')
                    ->label('Display Name')
                    ->description(fn (Employee $record): ?string => $record->calling_name ? "({$record->calling_name})" : null)
                    ->searchable(['name', 'calling_name'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('designation')
                    ->label('Designation')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('basic_salary')
                    ->label('Basic Salary')
                    ->money('LKR')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('fixed_allowance')
                    ->label('Fixed Allowance')
                    ->money('LKR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('epf_number')
                    ->label('EPF No')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('contact_number')
                    ->label('Contact')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('joined_date')
                    ->label('Joined Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('designation')
                    ->label('Designation')
                    ->options([
                        'Solar Technician' => 'Solar Technician',
                        'Site Supervisor' => 'Site Supervisor',
                        'Electrician' => 'Electrician',
                        'Office Staff' => 'Office Staff',
                        'Sales Engineer' => 'Sales Engineer',
                        'Project Manager' => 'Project Manager',
                        'Accountant' => 'Accountant',
                        'Driver' => 'Driver',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                Action::make('toggle_active')
                    ->label(fn (Employee $record): string => $record->is_active ? 'Mark Inactive' : 'Mark Active')
                    ->icon(fn (Employee $record): string => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Employee $record): string => $record->is_active ? 'warning' : 'success')
                    ->action(function (Employee $record): void {
                        $record->update(['is_active' => ! $record->is_active]);
                        Notification::make()
                            ->title('Employee status updated')
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
