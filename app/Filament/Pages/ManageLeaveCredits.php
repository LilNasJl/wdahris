<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Filament\Widgets\LeaveCreditEmployeeList;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;

class ManageLeaveCredits extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.manage-leave-credits';

    protected static ?string $title = 'Manage Leave Credits';

    protected static bool $shouldRegisterNavigation = false;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('return')
                ->label('Return')
                ->icon(Heroicon::ArrowLeft)
                ->url(LeaveResource::getUrl()),
        ];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            LeaveCreditEmployeeList::class,
        ];
    }
}
