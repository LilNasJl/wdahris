<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class DashboardCard extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int|array|null
    {
        return [
            'default' => 2,
            'sm' => 2,
            'md' => 3,
            'lg' => 6,
            'xl' => 6,
        ];
    }

    protected function getStats(): array
    {
        $activeCount = $this->activeEmployeesQuery()->count();
        $permanentCount = $this->activeEmployeesQuery()->where('employment_type', 'Permanent')->count();
        $casualCount = $this->activeEmployeesQuery()->where('employment_type', 'Casual')->count();
        $jobOrderCount = $this->activeEmployeesQuery()->where('employment_type', 'Job Order')->count();
        $temporaryCount = $this->activeEmployeesQuery()->where('employment_type', 'Temporary')->count();
        $coterminousCount = $this->activeEmployeesQuery()->where('employment_type', 'Coterminous')->count();

        return [
            Stat::make('Total Active', $activeCount)
                ->description('Active workforce')
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-s-user-group')
                ->color('primary')
                ->extraAttributes(['class' => 'wda-stat-card wda-stat-total-active']),

            Stat::make('Permanent', $permanentCount)
                ->description('Regular plantilla')
                ->descriptionIcon('heroicon-m-check-badge')
                ->icon('heroicon-s-identification')
                ->color('success')
                ->extraAttributes(['class' => 'wda-stat-card wda-stat-permanent']),

            Stat::make('Casual', $casualCount)
                ->description('Casual appointees')
                ->descriptionIcon('heroicon-m-briefcase')
                ->icon('heroicon-s-briefcase')
                ->color('warning')
                ->extraAttributes(['class' => 'wda-stat-card wda-stat-casual']),

            Stat::make('Job Order', $jobOrderCount)
                ->description('Contract of service')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->icon('heroicon-s-clipboard-document-list')
                ->color('info')
                ->extraAttributes(['class' => 'wda-stat-card wda-stat-job-order']),

            Stat::make('Temporary', $temporaryCount)
                ->description('Temporary status')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-s-clock')
                ->color('gray')
                ->extraAttributes(['class' => 'wda-stat-card wda-stat-temporary']),

            Stat::make('Coterminous', $coterminousCount)
                ->description('Co-terminus tenure')
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->icon('heroicon-s-arrows-right-left')
                ->color('danger')
                ->extraAttributes(['class' => 'wda-stat-card wda-stat-coterminous']),
        ];
    }

    private function activeEmployeesQuery(): Builder
    {
        return Employee::query()
            ->whereHas('user', fn (Builder $query): Builder => $query->where('role', 'employee'))
            ->where(function (Builder $query): void {
                $query->whereNull('employment_type')
                    ->orWhereNotIn('employment_type', [
                        'Retired',
                        'Resigned',
                        'Terminated',
                        'Force Resigned',
                        'Death of Employee',
                    ]);
            });
    }
}

