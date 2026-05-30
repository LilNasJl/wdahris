<?php

namespace App\Filament\Personnel\Pages;

use App\Filament\Personnel\Widgets\DashboardCards;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;

class Dashboard extends Page
{
    protected string $view = 'filament.personnel.pages.dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardCards::class,
        ];
    }
}
