<x-filament-panels::page>
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        /** @var \App\Models\Employee $employee */
        $employee = $user?->employee?->load(['designation', 'department']);

        $fullName = $this->getEmployeeName();
        $employeeId = $employee ? \App\Models\Employee::employeeIdFromUid($employee->uid) : $user?->username;
        $departmentName = $employee?->department?->name ?: 'Department Unassigned';
        $designationTitle = $employee?->designation?->title ?: 'Position Unassigned';

        // Avatar Initials
        $initials = collect(explode(' ', trim($fullName)))
            ->filter()
            ->map(fn($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
    @endphp

    <style>
        .wda-account-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }

        /* Hero Banner */
        .wda-account-hero {
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
            .wda-account-hero {
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
            width: 68px;
            height: 68px;
            min-width: 68px;
            border-radius: 16px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: 2px solid #38bdf8;
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .wda-hero-name {
            font-size: 21px;
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
            margin-top: 6px;
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

        .wda-hero-right {
            display: flex;
            align-items: center;
        }

        .wda-security-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.3);
            color: #7dd3fc;
            font-size: 12px;
            font-weight: 700;
        }

        /* Settings Card */
        .wda-settings-card {
            background: #0d1524;
            border: 1px solid #1a2942;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }

        .wda-card-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 20px 24px;
            background: #101a2c;
            border-bottom: 1px solid #1a2942;
        }

        .wda-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(2, 132, 199, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #38bdf8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wda-header-text h2 {
            font-size: 15px;
            font-weight: 800;
            color: #f1f5f9;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            margin: 0;
        }

        .wda-header-text p {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 2px 0 0 0;
        }

        .wda-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* Notice Banner */
        .wda-notice-banner {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            background: rgba(2, 132, 199, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 10px;
            color: #bae6fd;
            font-size: 12.5px;
            line-height: 1.5;
        }

        .wda-notice-icon {
            width: 18px;
            height: 18px;
            min-width: 18px;
            margin-top: 1px;
            color: #38bdf8;
        }

        /* Submit Button */
        .wda-action-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-top: 8px;
            border-top: 1px solid #1a2942;
        }

        .wda-submit-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 700;
            letter-spacing: 0.02em;
            border: 1px solid rgba(56, 189, 248, 0.5);
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .wda-submit-btn:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.55);
            transform: translateY(-1px);
        }

        .wda-submit-btn:active {
            transform: translateY(0);
        }
    </style>

    <div class="wda-account-container">
        <!-- Hero Profile Banner -->
        <div class="wda-account-hero">
            <div class="wda-hero-left">
                <div class="wda-hero-avatar">
                    {{ $initials ?: 'ME' }}
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
                    </div>
                </div>
            </div>

            <div class="wda-hero-right">
                <span class="wda-security-badge">
                    <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                    Credentials & Password Security
                </span>
            </div>
        </div>

        <!-- Account Credentials Settings Card -->
        <div class="wda-settings-card">
            <div class="wda-card-header">
                <div class="wda-icon-box">
                    <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div class="wda-header-text">
                    <h2>Account Credentials & Security</h2>
                    <p>Update your system login username, contact email, and security password.</p>
                </div>
            </div>

            <div class="wda-card-body">
                <!-- Informational Tip -->
                <div class="wda-notice-banner">
                    <svg class="wda-notice-icon" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <div>
                        <strong>Password Policy:</strong> Leave password fields blank if you do not want to change your current password. To change your password, enter at least 8 characters and confirm it below.
                    </div>
                </div>

                <form wire:submit="save" class="space-y-6">
                    {{ $this->form }}

                    <div class="wda-action-row">
                        <button type="submit" class="wda-submit-btn" wire:loading.attr="disabled">
                            <svg style="width: 17px; height: 17px;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span wire:loading.remove>Save Account Changes</span>
                            <span wire:loading>Saving Changes...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
