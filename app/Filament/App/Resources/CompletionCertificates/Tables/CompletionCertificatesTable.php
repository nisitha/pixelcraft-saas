<?php

namespace App\Filament\App\Resources\CompletionCertificates\Tables;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Models\CompletionCertificate;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompletionCertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (CompletionCertificate $record): string => CompletionCertificateResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('certificate_number')
                    ->label('Cert #')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('system_capacity')
                    ->label('Capacity & System')
                    ->limit(32)
                    ->searchable(),

                TextColumn::make('grid_connection')
                    ->label('Grid Type')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('commissioning_date')
                    ->label('Commissioned')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('paper_size')
                    ->label('Size')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->badge()
                    ->color('gray'),

                TextColumn::make('orientation')
                    ->label('Layout')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'landscape' ? 'primary' : 'warning'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'issued' => 'success',
                        'draft' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon(Heroicon::ArrowDownTray)
                    ->color('primary')
                    ->action(function (CompletionCertificate $record) {
                        $filename = PdfService::getCompletionCertificateFileName($record);

                        return response()->streamDownload(
                            fn () => print (app(PdfService::class)->generateCompletionCertificatePdf($record)->output()),
                            $filename,
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                ViewAction::make()
                    ->url(fn (CompletionCertificate $record): string => CompletionCertificateResource::getUrl('view', ['record' => $record])),

                EditAction::make()
                    ->visible(fn (): bool => auth()->user()?->isTenantOwnerOrAdmin() ?? false),
                DeleteAction::make()
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
