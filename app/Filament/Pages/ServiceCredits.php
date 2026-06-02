<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ServiceCreditsEmployeeList;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;
use UnitEnum;

class ServiceCredits extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.service-credits';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CircleStack;

    protected static string|UnitEnum|null $navigationGroup = 'History';

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            ServiceCreditsEmployeeList::class,
        ];
    }
}
