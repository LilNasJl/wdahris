<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\ServiceCredits;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ServiceCreditsProfileCards extends StatsOverviewWidget
{
    public int|string|array $columnSpan = 'full';

    public ?int $employeeId = null;

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
        $employee = Employee::query()
            ->with('designation')
            ->find($this->employeeId);

        $current = ServiceCredits::query()
            ->where('employee_id', $this->employeeId)
            ->latest('to_date')
            ->latest('from_date')
            ->latest()
            ->first();

        $hasServiceCredits = ServiceCredits::query()
            ->where('employee_id', $this->employeeId)
            ->exists();

        $designation = $current?->designation ?? $employee?->designation?->title ?? 'Not set';
        $status = $current?->status ?? $employee?->employment_type ?? 'Not set';
        $salary = $this->formatSalary($this->getSalaryValue($employee, $current, $hasServiceCredits));
        $branch = $current?->branch ?? 'Not set';

        return [
            $this->profileStat('Designation/Step', $designation, Heroicon::Briefcase),

            $this->profileStat('Status', $status, Heroicon::CheckBadge),

            $this->profileStat('Salary', $salary, Heroicon::Banknotes, 22),

            $this->profileStat('Branch', $branch, Heroicon::BuildingOffice),
        ];
    }

    protected function profileStat(string $label, mixed $value, mixed $icon, int $limit = 28): Stat
    {
        $text = filled($value) ? (string) $value : 'Not set';
        $shortText = e(Str::limit($text, $limit));

        return Stat::make($label, new HtmlString(
            '<span style="display:block;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:clamp(0.875rem, 3.4vw, 1.5rem);line-height:1.2;">'.$shortText.'</span>'
        ))
            ->icon($icon)
            ->extraAttributes([
                'title' => $text,
            ]);
    }

    protected function getSalaryValue(?Employee $employee, ?ServiceCredits $current, bool $hasServiceCredits): mixed
    {
        if (! $employee) {
            return null;
        }

        if (! $hasServiceCredits) {
            return (float) $employee->daily_rate * 22;
        }

        return $current?->salary ?? $employee->monthly_rate ?? ((float) $employee->daily_rate * 22);
    }

    protected function formatSalary(mixed $salary): string
    {
        if (blank($salary)) {
            return 'Not set';
        }

        return 'PHP '.number_format((float) $salary, 2);
    }
}
