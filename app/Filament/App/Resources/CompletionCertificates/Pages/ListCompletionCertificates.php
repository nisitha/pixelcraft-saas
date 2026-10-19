<?php

namespace App\Filament\App\Resources\CompletionCertificates\Pages;

use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompletionCertificates extends ListRecords
{
    protected static string $resource = CompletionCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Certificate'),
        ];
    }
}
