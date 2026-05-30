<?php

namespace App\Filament\Personnel\Widgets;

use App\Models\Leave;
use App\Services\LeaveCreditService;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardCards extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int|array|null
    {
        return [
            'default' => 2,
            'sm' => 2,
            'md' => 4,
            'lg' => 4,
            'xl' => 4,
        ];
    }

    protected function getStats(): array
    {
        $employee = Filament::auth()->user()?->employee;
        $credits = $employee
            ? LeaveCreditService::availableCredits((int) $employee->id)
            : ['vacation' => 0, 'sick' => 0, 'total' => 0];
        $pendingLeaveRequests = $employee
            ? Leave::query()
                ->where('employee_id', $employee->id)
                ->where('status', 'Pending')
                ->count()
            : 0;

        return [
            Stat::make('Vacation Leave Points', number_format($credits['vacation'], 2))
                ->icon(Heroicon::CalendarDays),

            Stat::make('Sick Leave Points', number_format($credits['sick'], 2))
                ->icon(Heroicon::Heart),

            Stat::make('Total Leave Credits', number_format($credits['total'], 2))
                ->icon(Heroicon::ClipboardDocumentList),

            Stat::make('Pending Leave Request', $pendingLeaveRequests)
                ->icon(Heroicon::PaperAirplane),

        ];
    }
}
