<?php

namespace App\Filament\Resources\AccountLogs\Pages;

use App\Filament\Resources\AccountLogs\AccountLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAccountLogs extends ListRecords
{
    protected static string $resource = AccountLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
