<?php

namespace App\Filament\Resources\SystemAccounts\Pages;

use App\Filament\Resources\SystemAccounts\SystemAccountResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditSystemAccount extends EditRecord
{
    protected static string $resource = SystemAccountResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = $data['username'];
        $data['role'] = $this->record->role === 'employee'
            ? 'hr'
            : $this->record->role;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (User $record): bool => Filament::auth()->id() !== $record->id),

            RestoreAction::make(),

            ForceDeleteAction::make()
                ->visible(fn (User $record): bool => Filament::auth()->id() !== $record->id),
        ];
    }
}
