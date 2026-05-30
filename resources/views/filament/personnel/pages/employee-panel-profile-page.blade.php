<x-filament-panels::page>
    @php
        $employee = auth()->user()?->employee?->load(['designation', 'department']);

        $basicInfo = [
            'First Name' => $employee?->firstname,
            'Middle Name' => $employee?->middlename,
            'Last Name' => $employee?->lastname,
            'Birth Date' => $employee?->birthdate?->format('M d, Y'),
            'Civil Status' => $employee?->status,
            'Gender' => $employee?->gender,
            'Mobile #' => $employee?->mobile,
            'No. of Kids' => $employee?->kids,
            'Permanent Address' => $employee?->address,
        ];

        $designationInfo = [
            'Designation' => $employee?->designation?->title,
            'Department' => $employee?->department?->name,
            'Hired Date' => $employee?->hired_date?->format('M d, Y'),
            'Employment Type' => $employee?->employment_type,
        ];

        $educationInfo = [
            'School Name' => $employee?->school_name,
            'Highest Level Attended' => $employee?->school_level,
            'Year Graduated' => $employee?->year_grad?->format('M d, Y'),
        ];

        $governmentInfo = [
            'GSIS BP #' => $employee?->gsis_no,
            'PhilHealth ID #' => $employee?->philhealth_no,
            'Pag-IBIG MID #' => $employee?->pagibig_no,
            'TIN #' => $employee?->tin,
            'SSS #' => $employee?->sss_no,
        ];

        $salaryInfo = [
            'Rate Type' => $employee?->rate_type,
            'Payment Type' => $employee?->payment_type,
            'Daily Rate' => filled($employee?->daily_rate) ? 'PHP ' . number_format((float) $employee->daily_rate, 2) : null,
            'Monthly Rate' => filled($employee?->monthly_rate) ? 'PHP ' . number_format((float) $employee->monthly_rate, 2) : null,
        ];

        $sections = [
            'Basic Info' => $basicInfo,
            'Government IDs' => $governmentInfo,
            'Designation' => $designationInfo,
            'Education' => $educationInfo,
            'Salary' => $salaryInfo,
        ];
    @endphp

    @if (! $employee)
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">No employee record is linked to this account.</p>
        </x-filament::section>
    @else
        @foreach ($sections as $heading => $items)
            <x-filament::section>
                <x-slot name="heading">{{ $heading }}</x-slot>

                <dl class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($items as $label => $value)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                {{ $label }}
                            </dt>
                            <dd class="mt-1 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-950 dark:border-white/10 dark:bg-white/5 dark:text-white">
                                {{ filled($value) ? $value : 'Not set' }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </x-filament::section>
        @endforeach
    @endif
</x-filament-panels::page>
