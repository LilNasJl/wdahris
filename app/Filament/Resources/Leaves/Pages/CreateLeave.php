<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Services\LeaveCreditService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            return [
                ...$data,
                ...LeaveCreditService::assertCanCreateLeave($data),
            ];
        } catch (ValidationException $exception) {
            Notification::make()
                ->danger()
                ->title('Unable to create leave request')
                ->body(collect($exception->errors())->flatten()->first())
                ->send();

            throw $exception;
        }
    }
}
