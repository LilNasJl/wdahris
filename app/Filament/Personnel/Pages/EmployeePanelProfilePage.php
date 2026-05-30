<?php

namespace App\Filament\Personnel\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class EmployeePanelProfilePage extends Page
{
    protected string $view = 'filament.personnel.pages.employee-panel-profile-page';

    protected static ?string $title = 'My Info';

    protected static ?string $navigationLabel = 'My Info';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;
}
