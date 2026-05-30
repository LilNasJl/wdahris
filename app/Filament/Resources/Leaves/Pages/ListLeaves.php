<?php

namespace App\Filament\Resources\Leaves\Pages;

use App\Filament\Pages\ManageLeaveCredits;
use App\Filament\Resources\Leaves\LeaveResource;
use App\Services\LeaveCreditService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'published' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Pending')),

            'draft' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Approved')),

            'archived' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Rejected')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageLeaveCredits')
                ->label('Manage Leave Credits')
                ->icon(Heroicon::ClipboardDocumentList)
                ->url(ManageLeaveCredits::getUrl()),

            CreateAction::make()
                ->label('New Leave Request')
                ->icon(Heroicon::PaperAirplane)
                ->mutateDataUsing(function (array $data): array {
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
                }),
        ];
    }
}
