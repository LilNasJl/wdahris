<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\RecordUpdatedNotification;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $employeeIdNumber = Employee::employeeIdFromUid($this->record->employee?->uid) ?? $this->record->username;

        $data['username'] = $employeeIdNumber;
        $data['name'] = $employeeIdNumber;
        $data['email'] = $this->systemEmailForUsername($employeeIdNumber);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
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
                'User updated',
                'A user record has been updated.'
            ));
        });
    }

    private function systemEmailForUsername(string $username): string
    {
        return strtolower(str_replace('-', '', $username)).'@wda.local';
    }
}
