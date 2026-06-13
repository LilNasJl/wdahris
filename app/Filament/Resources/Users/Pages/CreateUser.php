<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\RecordUpdatedNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $username = Employee::employeeIdFromUid($data['username'] ?? null) ?? (string) ($data['username'] ?? '');

        $data['role'] = 'employee';
        $data['username'] = $username;
        $data['name'] = $username;
        $data['email'] = $this->systemEmailForUsername($username);

        return $data;
    }

    protected function afterCreate(): void
    {
        $employeeIdNumber = Employee::employeeIdFromUid($this->record->employee?->uid) ?? $this->record->username;

        if (filled($employeeIdNumber) && $employeeIdNumber !== $this->record->username) {
            $this->record->forceFill([
                'username' => $employeeIdNumber,
                'name' => $employeeIdNumber,
                'email' => $this->systemEmailForUsername($employeeIdNumber),
            ])->save();
        }

        User::query()->each(function (User $user): void {
            $user->notify(new RecordUpdatedNotification(
                'User created',
                'A new user record has been created.'
            ));
        });
    }

    private function systemEmailForUsername(string $username): string
    {
        return strtolower(str_replace('-', '', $username)).'@wda.local';
    }
}
