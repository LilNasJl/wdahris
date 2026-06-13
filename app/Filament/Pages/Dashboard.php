<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BirthdayEmployeesTable;
use App\Filament\Widgets\DashboardCard;
use App\Filament\Widgets\ExpiringContractsTable;
use App\Filament\Widgets\NearRetirementEmployeesTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Dashboard extends Page
{
    protected string $view = 'filament.pages.dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;

    public function getHeaderWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardCard::class,
            NearRetirementEmployeesTable::class,
            ExpiringContractsTable::class,
            BirthdayEmployeesTable::class,
        ];
    }
}
