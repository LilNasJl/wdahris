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
        return [
            Stat::make('Total Active', $this->activeEmployeesQuery()->count())
                ->icon('heroicon-s-user-group')
                ->color('primary')
                ->extraAttributes(['class' => '[&_.fi-wi-stats-overview-stat-value]:text-primary-600 dark:[&_.fi-wi-stats-overview-stat-value]:text-primary-400']),

            Stat::make('Permanent', $this->activeEmployeesQuery()->where('employment_type', 'Permanent')->count())
                ->icon('heroicon-s-identification')
                ->color('success')
                ->extraAttributes(['class' => '[&_.fi-wi-stats-overview-stat-value]:text-success-600 dark:[&_.fi-wi-stats-overview-stat-value]:text-success-400']),

            Stat::make('Casual', $this->activeEmployeesQuery()->where('employment_type', 'Casual')->count())
                ->icon('heroicon-s-briefcase')
                ->color('warning')
                ->extraAttributes(['class' => '[&_.fi-wi-stats-overview-stat-value]:text-warning-600 dark:[&_.fi-wi-stats-overview-stat-value]:text-warning-400']),

            Stat::make('Job Order', $this->activeEmployeesQuery()->where('employment_type', 'Job Order')->count())
                ->icon('heroicon-s-clipboard-document-list')
                ->color('info')
                ->extraAttributes(['class' => '[&_.fi-wi-stats-overview-stat-value]:text-info-600 dark:[&_.fi-wi-stats-overview-stat-value]:text-info-400']),

            Stat::make('Temporary', $this->activeEmployeesQuery()->where('employment_type', 'Temporary')->count())
                ->icon('heroicon-s-clock')
                ->color('gray')
                ->extraAttributes(['class' => '[&_.fi-wi-stats-overview-stat-value]:text-gray-600 dark:[&_.fi-wi-stats-overview-stat-value]:text-gray-300']),

            Stat::make('Coterminous', $this->activeEmployeesQuery()->where('employment_type', 'Coterminous')->count())
                ->icon('heroicon-s-arrows-right-left')
                ->color('danger')
                ->extraAttributes(['class' => '[&_.fi-wi-stats-overview-stat-value]:text-danger-600 dark:[&_.fi-wi-stats-overview-stat-value]:text-danger-400']),
        ];
    }

    private function activeEmployeesQuery(): Builder
    {
        return Employee::query()
            ->whereHas('user', fn (Builder $query): Builder => $query->where('role', 'employee'))
            ->whereNotIn('employment_type', [
                'Retired',
                'Resigned',
                'Terminated',
                'Force Resigned',
                'Death of Employee',
            ]);
    }
}
