<?php

namespace App\Filament\App\Resources\Expenses\Pages;

use App\Filament\App\Resources\Expenses\ExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
}
