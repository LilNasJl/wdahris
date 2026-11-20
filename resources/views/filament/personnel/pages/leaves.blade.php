<x-filament-panels::page>
    @php
        /** @var \App\Models\User $user */
        $user = auth()->user();
        /** @var \App\Models\Employee $employee */
        $employee = $user?->employee;

        $credits = $employee
            ? \App\Services\LeaveCreditService::availableCredits((int) $employee->id)
            : ['vacation' => 0, 'sick' => 0, 'total' => 0];

        $pendingCount = $employee
            ? \App\Models\Leave::query()
                ->where('employee_id', $employee->id)
                ->where('status', 'Pending')
                ->count()
            : 0;
    @endphp

    <style>
        .wda-leaves-hero {
            position: relative;
            background: linear-gradient(135deg, #0b1f3d 0%, #102e5b 50%, #0d2448 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 16px;
            padding: 22px 28px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px -5px rgba(2, 20, 60, 0.4), 0 0 0 1px rgba(56, 189, 248, 0.1);
            overflow: hidden;
        }

        .wda-leaves-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -15%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.16) 0%, rgba(29, 104, 216, 0.05) 50%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .wda-leaves-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .wda-leaves-left {
            max-width: 550px;
        }

        .wda-leaves-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 9999px;
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #bae6fd;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .wda-leaves-title {
            font-size: clamp(20px, 2vw, 26px);
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin: 0 0 6px 0;
            line-height: 1.2;
        }

        .wda-leaves-subtitle {
            font-size: 13px;
            color: #93c5fd;
            margin: 0;
            font-weight: 400;
            line-height: 1.45;
        }

        /* Balance Chips */
        .wda-balance-chips {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .wda-chip {
            display: flex;
            flex-direction: column;
            gap: 3px;
            background: rgba(9, 24, 46, 0.7);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 12px;
            padding: 10px 16px;
            min-width: 105px;
            backdrop-filter: blur(8px);
        }

        .wda-chip-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
        }

        .wda-chip-val {
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
        }

        .wda-chip-vl {
            border-top: 3px solid #38bdf8;
        }
        .wda-chip-vl .wda-chip-val { color: #38bdf8; }

        .wda-chip-sl {
            border-top: 3px solid #34d399;
        }
        .wda-chip-sl .wda-chip-val { color: #34d399; }

        .wda-chip-total {
            border-top: 3px solid #a78bfa;
        }
        .wda-chip-total .wda-chip-val { color: #c4b5fd; }

        .wda-chip-pending {
            border-top: 3px solid #fbbf24;
        }
        .wda-chip-pending .wda-chip-val { color: #fde047; }

        /* Tables Widget Container Theme */
        .fi-wi-table > .fi-ta-ctn {
            background: linear-gradient(145deg, #0b172a 0%, #0f223d 100%) !important;
            border: 1px solid rgba(56, 189, 248, 0.2) !important;
            border-radius: 14px !important;
            box-shadow: 0 4px 18px -2px rgba(2, 6, 23, 0.45) !important;
            overflow: hidden !important;
        }

        .fi-ta-header {
            background: rgba(14, 28, 49, 0.75) !important;
            border-bottom: 1px solid rgba(56, 189, 248, 0.15) !important;
            padding: 16px 20px !important;
        }

        .fi-ta-header-heading {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            letter-spacing: -0.01em !important;
        }

        .fi-ta-header-description {
            font-size: 12px !important;
            color: #94a3b8 !important;
            margin-top: 2px !important;
        }

        .fi-ta-header-cell {
            background: rgba(11, 23, 42, 0.85) !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            color: #7dd3fc !important;
            border-bottom: 1px solid rgba(56, 189, 248, 0.14) !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }

        .fi-ta-row {
            transition: background 0.15s ease !important;
        }

        .fi-ta-row:hover {
            background: rgba(30, 58, 102, 0.3) !important;
        }

        .fi-ta-cell {
            border-bottom: 1px solid rgba(56, 189, 248, 0.07) !important;
            color: #e2e8f0 !important;
            font-size: 13px !important;
        }

        .fi-ta-empty-state-icon-ctn {
            background: rgba(56, 189, 248, 0.1) !important;
            border-radius: 9999px !important;
            padding: 14px !important;
            border: 1px solid rgba(56, 189, 248, 0.2) !important;
        }

        .fi-ta-empty-state-icon-ctn svg {
            color: #38bdf8 !important;
            width: 32px !important;
            height: 32px !important;
        }

        .fi-ta-empty-state-heading {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            margin-top: 12px !important;
        }

        .fi-ta-empty-state-description {
            color: #94a3b8 !important;
            font-size: 12.5px !important;
            max-width: 420px !important;
            margin-inline: auto !important;
            margin-top: 4px !important;
            line-height: 1.5 !important;
        }
    </style>

    <!-- Leaves Hero Header -->
    <div class="wda-leaves-hero">
        <div class="wda-leaves-content">
            <div class="wda-leaves-left">
                <div class="wda-leaves-pill">
                    <svg style="width: 13px; height: 13px;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                    <span>Employee Leave & Attendance</span>
                </div>
                <h1 class="wda-leaves-title">
                    My Leave Requests & Balance
                </h1>
                <p class="wda-leaves-subtitle">
                    Submit applications, track approval statuses, and monitor your available leave point credits.
                </p>
            </div>

            <div class="wda-balance-chips">
                <div class="wda-chip wda-chip-vl">
                    <span class="wda-chip-label">Vacation</span>
                    <span class="wda-chip-val">{{ number_format($credits['vacation'], 3) }}</span>
                </div>
                <div class="wda-chip wda-chip-sl">
                    <span class="wda-chip-label">Sick</span>
                    <span class="wda-chip-val">{{ number_format($credits['sick'], 3) }}</span>
                </div>
                <div class="wda-chip wda-chip-total">
                    <span class="wda-chip-label">Total Points</span>
                    <span class="wda-chip-val">{{ number_format($credits['total'], 3) }}</span>
                </div>
                <div class="wda-chip wda-chip-pending">
                    <span class="wda-chip-label">Pending</span>
                    <span class="wda-chip-val">{{ $pendingCount }}</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
