<?php

namespace App\Filament\SuperAdmin\Resources\AuditLogs\Pages;

use App\Filament\SuperAdmin\Resources\AuditLogs\AuditLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditLog extends CreateRecord
{
    protected static string $resource = AuditLogResource::class;
}
