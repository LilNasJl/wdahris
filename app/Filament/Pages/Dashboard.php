<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardCard;
use App\Filament\Widgets\EmploymentTypeHeadCount;
use App\Filament\Widgets\HeadCountPerDept;
use App\Filament\Widgets\PendingLeaveRequestsTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Dashboard extends Page
{
    protected string $view = 'filament.pages.dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;

    // protected function getColumns(): int | array
    // {
    //     return 2;
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardCard::class,
            PendingLeaveRequestsTable::class,
            HeadCountPerDept::class,
            EmploymentTypeHeadCount::class,
        ];
    }
}
