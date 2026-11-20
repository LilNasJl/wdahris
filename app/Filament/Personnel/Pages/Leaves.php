<?php

namespace App\Filament\Personnel\Pages;

use App\Filament\Personnel\Widgets\MyLeaveRequestsTable;
use App\Filament\Support\PersonnelLeaveForm;
use App\Models\Leave as LeaveRecord;
use App\Services\LeaveCreditService;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use Override;

class Leaves extends Page
{
    protected string $view = 'filament.personnel.pages.leaves';

    protected static ?string $title = 'Leaves Request';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PaperAirplane;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('sendLeaveRequest')
                ->label('Send Leave Request')
                ->icon(Heroicon::PaperAirplane)
                ->color('primary')
                ->model(LeaveRecord::class)
                ->modalHeading('Send Leave Request')
                ->schema(PersonnelLeaveForm::schema())
                ->mutateDataUsing(function (array $data): array {
                    $employee = Filament::auth()->user()?->employee;

                    $data['employee_id'] = $employee?->id;

                    try {
                        return [
                            ...$data,
                            ...LeaveCreditService::assertCanCreateLeave($data),
                        ];
                    } catch (ValidationException $exception) {
                        Notification::make()
                            ->danger()
                            ->title('Unable to send leave request')
                            ->body(collect($exception->errors())->flatten()->first())
                            ->send();

                        throw $exception;
                    }
                })
                ->createAnother(false)
                ->successNotificationTitle('Leave request sent'),
        ];
    }

    #[Override]
    protected function getFooterWidgets(): array
    {
        return [
            MyLeaveRequestsTable::class,
        ];
    }
}
