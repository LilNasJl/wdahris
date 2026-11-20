<x-filament-panels::page>
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        /** @var \App\Models\Employee $employee */
        $employee = $user?->employee?->load(['designation', 'department']);

        $fullName = $employee?->full_name ?: ($user?->name ?: 'Employee Profile');
        $employeeId = $employee ? \App\Models\Employee::employeeIdFromUid($employee->uid) : $user?->username;
        $departmentName = $employee?->department?->name ?: 'Department Unassigned';
        $designationTitle = $employee?->designation?->title ?: 'Position Unassigned';
        $hiredDateFormatted = $employee?->hired_date?->format('M d, Y');
        $yearsOfService = $employee?->hired_date ? $employee->hired_date->diffInYears(now()) . ' yrs (' . $employee->hired_date->diffForHumans(null, true) . ')' : null;
        $birthDateFormatted = $employee?->birthdate?->format('M d, Y');
        $ageFormatted = $employee?->birthdate ? $employee->birthdate->age . ' years old' : null;

        // Initials for avatar
        $initials = collect(explode(' ', trim($fullName)))
            ->filter()
            ->map(fn($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');

        $sections = [
            [
                'title' => 'Personal Identification & Bio',
                'icon' => 'user',
                'accent' => '#0284c7',
                'items' => [
                    'First Name' => $employee?->firstname,
                    'Middle Name' => $employee?->middlename,
                    'Last Name' => $employee?->lastname,
                    'Name Extension' => $employee?->name_extension,
                    'Birth Date' => $birthDateFormatted,
                    'Age' => $ageFormatted,
                    'Gender' => $employee?->gender,
                    'Civil Status' => $employee?->status,
                    'Citizenship' => $employee?->citizenship,
                    'Blood Type' => $employee?->blood_type,
                    'Height (cm)' => $employee?->height_cm ? $employee->height_cm . ' cm' : null,
                    'Weight (kg)' => $employee?->weight_kg ? $employee->weight_kg . ' kg' : null,
                ],
            ],
            [
                'title' => 'Contact & Residence',
                'icon' => 'phone',
                'accent' => '#0ea5e9',
                'items' => [
                    'Mobile #' => $employee?->mobile,
                    'Personal Email' => $employee?->email,
                    'No. of Kids' => $employee?->kids !== null ? (string)$employee->kids : null,
                    'Residential Address' => $employee?->residential_address,
                    'Permanent Address' => $employee?->permanent_address ?: $employee?->address,
                    'Place of Birth' => $employee?->place_of_birth,
                ],
            ],
            [
                'title' => 'Position & Employment Details',
                'icon' => 'briefcase',
                'accent' => '#10b981',
                'items' => [
                    'Employee ID #' => $employeeId,
                    'Designation' => $designationTitle,
                    'Department' => $departmentName,
                    'Date Hired' => $hiredDateFormatted,
                    'Length of Service' => $yearsOfService,
                    'Employment Type' => $employee?->employment_type,
                    'Classification' => $employee?->employment_classification,
                    'Plantilla Item #' => $employee?->plantilla_item_number,
                    'Office Assignment' => $employee?->office_assignment,
                    'Immediate Supervisor' => $employee?->immediate_supervisor,
                    'Original Appointment' => $employee?->original_appointment_date?->format('M d, Y'),
                    'Assumption Date' => $employee?->assumption_date?->format('M d, Y'),
                ],
            ],
            [
                'title' => 'Government Identification & Contributions',
                'icon' => 'shield',
                'accent' => '#8b5cf6',
                'items' => [
                    'GSIS BP #' => $employee?->gsis_no ?: $employee?->gsis,
                    'PhilHealth ID #' => $employee?->philhealth_no ?: $employee?->philhealth,
                    'Pag-IBIG MID #' => $employee?->pagibig_no ?: $employee?->pagibig,
                    'TIN #' => $employee?->tin,
                    'SSS #' => $employee?->sss_no ?: $employee?->sss,
                    'PhilSys Card #' => $employee?->philsys,
                ],
            ],
            [
                'title' => 'Compensation & Salary Grade',
                'icon' => 'banknotes',
                'accent' => '#f59e0b',
                'items' => [
                    'Salary Grade' => $employee?->salary_grade ? 'SG ' . $employee->salary_grade : null,
                    'Salary Step' => $employee?->salary_step ? 'Step ' . $employee->salary_step : null,
                    'Rate Type' => $employee?->rate_type,
                    'Payment Type' => $employee?->payment_type,
                    'Daily Rate' => filled($employee?->daily_rate) ? 'PHP ' . number_format((float) $employee->daily_rate, 2) : null,
                    'Monthly Rate' => filled($employee?->monthly_rate) ? 'PHP ' . number_format((float) $employee->monthly_rate, 2) : null,
                    'Allowance' => filled($employee?->allowance) ? 'PHP ' . number_format((float) $employee->allowance, 2) : null,
                ],
            ],
            [
                'title' => 'Educational Background',
                'icon' => 'academic-cap',
                'accent' => '#14b8a6',
                'items' => [
                    'School / Institution' => $employee?->school_name,
                    'Highest Level Attended' => $employee?->school_level,
                    'Year Graduated' => $employee?->year_grad?->format('Y') ?: $employee?->year_grad?->format('M d, Y'),
                ],
            ],
        ];
    @endphp

    <style>
        .wda-profile-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }

        /* Hero Banner */
        .wda-hero-banner {
            position: relative;
            background: linear-gradient(135deg, #0b1f3d 0%, #09172c 60%, #060e1d 100%);
            border: 1px solid #1e3a63;
            border-radius: 16px;
            padding: 24px 28px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .wda-hero-banner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 28px 32px;
            }
        }

        .wda-hero-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .wda-hero-avatar {
            width: 72px;
            height: 72px;
            min-width: 72px;
            border-radius: 16px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: 2px solid #38bdf8;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .wda-hero-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .wda-hero-name {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin: 0;
        }

        .wda-hero-pills {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-top: 2px;
        }

        .wda-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1;
        }

        .wda-pill-id {
            background: rgba(2, 132, 199, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.4);
            color: #38bdf8;
        }

        .wda-pill-dept {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
        }

        .wda-pill-title {
            background: rgba(148, 163, 184, 0.15);
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: #cbd5e1;
        }

        .wda-hero-right {
            display: flex;
            gap: 16px;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 16px;
        }

        @media (min-width: 768px) {
            .wda-hero-right {
                border-top: none;
                padding-top: 0;
                border-left: 1px solid rgba(255, 255, 255, 0.08);
                padding-left: 24px;
            }
        }

        .wda-quick-stat {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .wda-stat-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.05em;
        }

        .wda-stat-value {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
        }

        .wda-stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #34d399;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 4px 10px;
            border-radius: 9999px;
            width: fit-content;
        }

        .wda-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        /* Section Cards */
        .wda-section-card {
            background: #0d1524;
            border: 1px solid #1a2942;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
            transition: border-color 0.2s ease;
        }

        .wda-section-card:hover {
            border-color: #263e63;
        }

        .wda-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: #101a2c;
            border-bottom: 1px solid #1a2942;
        }

        .wda-card-icon-wrap {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wda-card-title {
            font-size: 14px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: -0.01em;
            margin: 0;
            text-transform: uppercase;
        }

        .wda-card-body {
            padding: 20px;
        }

        /* Data Grid */
        .wda-data-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        @media (min-width: 640px) {
            .wda-data-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .wda-data-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .wda-data-tile {
            background: #121c2e;
            border: 1px solid #1c2b42;
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .wda-data-tile:hover {
            border-color: #2b4266;
            background: #142036;
        }

        .wda-tile-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #7dd3fc;
        }

        .wda-tile-value {
            font-size: 13.5px;
            font-weight: 600;
            color: #f8fafc;
            line-height: 1.35;
            word-break: break-word;
        }

        .wda-tile-empty {
            font-size: 12px;
            font-style: italic;
            color: #64748b;
        }
    </style>

    <div class="wda-profile-container">
        <!-- Hero Header Profile Banner -->
        <div class="wda-hero-banner">
            <div class="wda-hero-left">
                <div class="wda-hero-avatar">
                    {{ $initials ?: 'WDA' }}
                </div>
                <div class="wda-hero-details">
                    <h1 class="wda-hero-name">{{ $fullName }}</h1>
                    <div class="wda-hero-pills">
                        <span class="wda-pill wda-pill-id">
                            <svg style="width: 13px; height: 13px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            ID # {{ $employeeId }}
                        </span>
                        <span class="wda-pill wda-pill-dept">
                            <svg style="width: 13px; height: 13px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h3a1.5 1.5 0 0 1 1.5 1.5V21" />
                            </svg>
                            {{ $departmentName }}
                        </span>
                        <span class="wda-pill wda-pill-title">
                            <svg style="width: 13px; height: 13px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                            </svg>
                            {{ $designationTitle }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="wda-hero-right">
                <div class="wda-quick-stat">
                    <span class="wda-stat-label">System Access</span>
                    <span class="wda-stat-badge">
                        <span class="wda-badge-dot"></span>
                        Active Personnel
                    </span>
                </div>
                @if ($hiredDateFormatted)
                    <div class="wda-quick-stat">
                        <span class="wda-stat-label">Date Hired</span>
                        <span class="wda-stat-value">{{ $hiredDateFormatted }}</span>
                    </div>
                @endif
            </div>
        </div>

        @if (! $employee)
            <div class="wda-section-card" style="padding: 24px; text-align: center; color: #94a3b8;">
                <p style="margin: 0;">No personnel 201 profile record is currently linked to this user account.</p>
            </div>
        @else
            <!-- Structured Sections -->
            @foreach ($sections as $section)
                <div class="wda-section-card">
                    <div class="wda-card-header">
                        <div class="wda-card-icon-wrap" style="background: {{ $section['accent'] }}22; color: {{ $section['accent'] }};">
                            @if ($section['icon'] === 'user')
                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            @elseif ($section['icon'] === 'phone')
                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                            @elseif ($section['icon'] === 'briefcase')
                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                </svg>
                            @elseif ($section['icon'] === 'shield')
                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            @elseif ($section['icon'] === 'banknotes')
                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75a.75.75 0 0 1 .75.75v.75m0-9.75H21a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-.75.75H3.75m17.25-9.75h.75a.75.75 0 0 1 .75.75v8.25a.75.75 0 0 1-.75.75h-.75m-15.75 0a.75.75 0 0 1-.75-.75V7.5a.75.75 0 0 1 .75-.75h15a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-.75.75h-15Z" />
                                </svg>
                            @else
                                <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-5.25 6.557c0 1.554.896 2.923 2.25 3.59" />
                                </svg>
                            @endif
                        </div>
                        <h2 class="wda-card-title">{{ $section['title'] }}</h2>
                    </div>

                    <div class="wda-card-body">
                        <div class="wda-data-grid">
                            @foreach ($section['items'] as $label => $value)
                                <div class="wda-data-tile">
                                    <span class="wda-tile-label">{{ $label }}</span>
                                    @if (filled($value))
                                        <span class="wda-tile-value">{{ $value }}</span>
                                    @else
                                        <span class="wda-tile-empty">Not set</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</x-filament-panels::page>
