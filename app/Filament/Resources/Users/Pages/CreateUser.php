<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Notifications\RecordUpdatedNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'employee';
        $data['name'] = $data['username'];

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->employee?->update(['email' => $this->record->email]);

        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new RecordUpdatedNotification(
                'User created',
                'A new user record has been created.'
            ));
        }
    }
}
