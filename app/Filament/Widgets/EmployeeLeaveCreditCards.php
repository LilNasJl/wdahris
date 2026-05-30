<?php

namespace App\Filament\Widgets;

use App\Services\LeaveCreditService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeLeaveCreditCards extends StatsOverviewWidget
{
    public int|string|array $columnSpan = 'full';

    public ?int $employeeId = null;

    protected function getColumns(): int|array|null
    {
        return [
            'default' => 3,
            'sm' => 3,
            'md' => 3,
            'lg' => 3,
            'xl' => 3,
        ];
    }

    protected function getStats(): array
    {
        $credits = LeaveCreditService::availableCredits((int) $this->employeeId);

        return [
            Stat::make('Vacation Leave', number_format($credits['vacation'], 2))
                ->icon(Heroicon::CalendarDays),

            Stat::make('Sick Leave', number_format($credits['sick'], 2))
                ->icon(Heroicon::Heart),

            Stat::make('Leave Credits', number_format($credits['total'], 2))
                ->icon(Heroicon::ClipboardDocumentList),
        ];
    }
}
