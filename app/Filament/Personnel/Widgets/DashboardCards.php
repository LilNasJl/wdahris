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
    protected static ?int $sort = 1;

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
            Stat::make('Vacation Leave Points', number_format($credits['vacation'], 3))
                ->description('Available VL balance')
                ->descriptionIcon(Heroicon::CalendarDays)
                ->color('info'),

            Stat::make('Sick Leave Points', number_format($credits['sick'], 3))
                ->description('Available SL balance')
                ->descriptionIcon(Heroicon::Heart)
                ->color('success'),

            Stat::make('Total Leave Credits', number_format($credits['total'], 3))
                ->description('Combined available points')
                ->descriptionIcon(Heroicon::ClipboardDocumentList)
                ->color('primary'),

            Stat::make('Pending Leave Requests', (string) $pendingLeaveRequests)
                ->description($pendingLeaveRequests > 0 ? 'Awaiting HR approval' : 'All requests processed')
                ->descriptionIcon($pendingLeaveRequests > 0 ? Heroicon::Clock : Heroicon::CheckBadge)
                ->color($pendingLeaveRequests > 0 ? 'warning' : 'gray'),
        ];
    }
}
