@php
    /** @var \App\Models\User $user */
    $user = auth()->user();
    /** @var \App\Models\Employee $employee */
    $employee = $user?->employee?->load(['designation', 'department']);

    $fullName = $employee?->full_name ?: ($user?->name ?: 'Employee');
    $employeeId = $employee ? \App\Models\Employee::employeeIdFromUid($employee->uid) : $user?->username;
    $departmentName = $employee?->department?->name ?: 'Department Unassigned';
    $designationTitle = $employee?->designation?->title ?: 'Position Unassigned';
@endphp

<style>
    /* =====================================================================
       Personnel Dashboard Header & Quick Action Tiles
       Dark Ocean Theme (#0b1f3d, #102e5b, #0d2448, cyan #38bdf8)
       ===================================================================== */
    .wda-personnel-header-wrap {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-bottom: 8px;
    }

    /* Personnel Dashboard Hero Banner */
    .wda-personnel-hero {
        position: relative;
        background: linear-gradient(135deg, #0b1f3d 0%, #102e5b 50%, #0d2448 100%);
        border: 1px solid rgba(56, 189, 248, 0.25);
        border-radius: 16px;
        padding: 22px 28px;
        box-shadow: 0 10px 30px -5px rgba(2, 20, 60, 0.4), 0 0 0 1px rgba(56, 189, 248, 0.1);
        overflow: hidden;
    }

    .wda-personnel-hero::before {
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

    .wda-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .wda-hero-left {
        max-width: 650px;
    }

    .wda-hero-pill {
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
        margin-bottom: 10px;
    }

    .wda-hero-title {
        font-size: clamp(22px, 2.2vw, 28px);
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.025em;
        margin: 0 0 6px 0;
        line-height: 1.2;
        text-shadow: 0 2px 10px rgba(0, 20, 60, 0.3);
    }

    .wda-hero-subtitle {
        font-size: 13.5px;
        color: #93c5fd;
        margin: 0 0 12px 0;
        font-weight: 400;
        line-height: 1.45;
    }

    .wda-hero-tags {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .wda-tag-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.2;
    }

    .wda-tag-id {
        background: rgba(2, 132, 199, 0.25);
        border: 1px solid rgba(56, 189, 248, 0.45);
        color: #7dd3fc;
    }

    .wda-tag-dept {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #6ee7b7;
    }

    .wda-tag-title {
        background: rgba(148, 163, 184, 0.2);
        border: 1px solid rgba(148, 163, 184, 0.35);
        color: #e2e8f0;
    }

    .wda-hero-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .wda-hero-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 15px;
        border-radius: 10px;
        background: rgba(9, 25, 48, 0.65);
        border: 1px solid rgba(56, 189, 248, 0.25);
        color: #e0f2fe;
        font-size: 12.5px;
        font-weight: 600;
        backdrop-filter: blur(8px);
    }

    .wda-hero-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 15px;
        border-radius: 10px;
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(52, 211, 153, 0.35);
        color: #a7f3d0;
        font-size: 12.5px;
        font-weight: 700;
    }

    .wda-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background-color: #34d399;
        box-shadow: 0 0 10px #34d399;
    }

    /* Quick Action Tiles */
    .wda-quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 14px;
    }

    @media (min-width: 640px) {
        .wda-quick-actions-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .wda-action-tile {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 14px;
        background: #0d1726;
        border: 1px solid #1c2e4a;
        color: #f1f5f9;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        transition: all 0.2s ease;
    }

    .wda-action-tile:hover {
        background: #112038;
        border-color: #38bdf8;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(2, 132, 199, 0.25);
    }

    .wda-action-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wda-action-texts {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .wda-action-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .wda-action-desc {
        font-size: 11.5px;
        color: #94a3b8;
        margin: 0;
    }
</style>

<div class="wda-personnel-header-wrap">
    <!-- Personnel Dashboard Hero Banner -->
    <div class="wda-personnel-hero">
        <div class="wda-hero-content">
            <div class="wda-hero-left">
                <div class="wda-hero-pill">
                    <svg style="width: 13px; height: 13px;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    <span>Personnel Self-Service Portal</span>
                </div>
                <h1 class="wda-hero-title">
                    Welcome back, {{ $employee?->firstname ?: $fullName }}!
                </h1>
                <p class="wda-hero-subtitle">
                    Water District Asuncion &bull; Employee Self-Service &amp; Leave Portal
                </p>
                <div class="wda-hero-tags">
                    <span class="wda-tag-pill wda-tag-id">
                        <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        ID # {{ $employeeId }}
                    </span>
                    <span class="wda-tag-pill wda-tag-dept">
                        <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.75a1.5 1.5 0 0 1 1.5-1.5h3a1.5 1.5 0 0 1 1.5 1.5V21" />
                        </svg>
                        {{ $departmentName }}
                    </span>
                    <span class="wda-tag-pill wda-tag-title">
                        <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                        </svg>
                        {{ $designationTitle }}
                    </span>
                </div>
            </div>

            <div class="wda-hero-right">
                <div class="wda-hero-meta-badge">
                    <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                    </svg>
                    <span>{{ now()->format('l, F d, Y') }}</span>
                </div>
                <div class="wda-hero-status-pill">
                    <span class="wda-pulse-dot"></span>
                    <span>Portal Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="wda-quick-actions-grid">
        <a href="{{ route('filament.personnel.pages.leaves') }}" class="wda-action-tile">
            <div class="wda-action-icon" style="background: rgba(2, 132, 199, 0.2); color: #38bdf8;">
                <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </div>
            <div class="wda-action-texts">
                <h3 class="wda-action-title">File Leave Request</h3>
                <p class="wda-action-desc">Submit new leave application</p>
            </div>
        </a>

        <a href="{{ route('filament.personnel.pages.employee-panel-profile-page') }}" class="wda-action-tile">
            <div class="wda-action-icon" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">
                <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <div class="wda-action-texts">
                <h3 class="wda-action-title">My 201 Info</h3>
                <p class="wda-action-desc">View personal & service records</p>
            </div>
        </a>

        <a href="{{ route('filament.personnel.pages.my-account') }}" class="wda-action-tile">
            <div class="wda-action-icon" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa;">
                <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <div class="wda-action-texts">
                <h3 class="wda-action-title">Account Security</h3>
                <p class="wda-action-desc">Update password & credentials</p>
            </div>
        </a>
    </div>
</div>
