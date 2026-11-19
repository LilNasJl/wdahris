<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class HeadCountPerDept extends ChartWidget
{
    protected ?string $heading = 'Head Count Per Department';
    protected int|string|array $columnSpan = 1;
    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $data = Employee::select('department_id', DB::raw('count(*) as total'))
            ->whereHas('user', function ($query) {
                $query->where('role', 'employee');
            })
            ->groupBy('department_id')
            ->with('department')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Employees',
                    'data' => $data->pluck('total'),
                    'backgroundColor' => [
                        '#1d68d8',
                        '#0284c7',
                        '#0ea5e9',
                        '#38bdf8',
                        '#60a5fa',
                    ],
                    'borderColor' => '#38bdf8',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $data->map(function ($item) {
                return $item->department->acronym ?? $item->department->name ?? 'Unassigned';
            }),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}


