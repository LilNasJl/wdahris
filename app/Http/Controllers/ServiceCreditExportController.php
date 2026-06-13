<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ServiceCredits;
use Illuminate\Support\Str;

class ServiceCreditExportController extends Controller
{
    public function __invoke(Employee $employee, string $format)
    {
        abort_unless(in_array($format, ['pdf', 'excel', 'print'], true), 404);

        $employee->load(['designation', 'department', 'serviceCreditSignatory']);

        $records = $employee->serviceCredits()
            ->oldest('from_date')
            ->oldest('to_date')
            ->oldest()
            ->get();

        $current = ServiceCredits::query()
            ->where('employee_id', $employee->id)
            ->latest('to_date')
            ->latest('from_date')
            ->latest()
            ->first();

        $viewData = [
            'employee' => $employee,
            'records' => $records,
            'currentDetails' => [
                'designation' => $current?->designation ?? $employee->designation?->title ?? '',
                'status' => $current?->status ?? $employee->employment_type ?? '',
                'salary' => $this->formatSalary($current?->salary ?? $employee->daily_rate),
                'branch' => $current?->branch ?? '',
            ],
            'generatedDate' => now()->format('F d, Y'),
            'signatory' => $employee->serviceCreditSignatory,
            'format' => $format,
            'autoPrint' => in_array($format, ['pdf', 'print'], true),
        ];

        if ($format === 'excel') {
            $filename = 'service-credits-'.Str::slug($employee->full_name ?: 'employee-'.$employee->id).'.xls';

            return response()
                ->view('exports.service-credits-excel', $viewData)
                ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        }

        return response()->view('exports.service-credits-print', $viewData);
    }

    protected function formatSalary(mixed $salary): string
    {
        if (blank($salary)) {
            return '';
        }

        return 'PHP '.number_format((float) $salary, 2);
    }
}
