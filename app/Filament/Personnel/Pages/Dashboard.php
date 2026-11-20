<?php

namespace App\Filament\Personnel\Pages;

use App\Filament\Personnel\Widgets\DashboardCards;
use App\Filament\Personnel\Widgets\MyLeaveRequestsTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Override;

class Dashboard extends Page
{
    protected string $view = 'filament.personnel.pages.dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;

    public function getHeader(): ?View
    {
        return view('filament.personnel.pages.dashboard-header');
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardCards::class,
            MyLeaveRequestsTable::class,
        ];
    }
}
