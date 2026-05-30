<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Services\LeaveCreditService;
use Illuminate\Support\Str;

class LeaveCreditExportController extends Controller
{
    public function __invoke(Employee $employee, string $report, string $format)
    {
        abort_unless(in_array($report, ['leave-history', 'credit-history'], true), 404);
        abort_unless(in_array($format, ['pdf', 'excel', 'print'], true), 404);

        $employee->load(['designation', 'department']);

        $viewData = [
            'employee' => $employee,
            'report' => $report,
            'format' => $format,
            'generatedDate' => now()->format('F d, Y'),
            'autoPrint' => in_array($format, ['pdf', 'print'], true),
            'title' => $report === 'leave-history' ? 'Leave History' : 'Leave Credits History',
            'leaveRows' => $report === 'leave-history'
                ? Leave::query()
                    ->where('employee_id', $employee->id)
                    ->latest('created_at')
                    ->get()
                : collect(),
            'creditRows' => $report === 'credit-history'
                ? LeaveCreditService::ledgerRows($employee->id)
                : [],
        ];

        if ($format === 'excel') {
            $filename = Str::slug($viewData['title'].'-'.$employee->full_name).'.xls';

            return response()
                ->view('exports.leave-credits-excel', $viewData)
                ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        }

        return response()->view('exports.leave-credits-print', $viewData);
    }
}
