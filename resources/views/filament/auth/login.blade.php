@php
    $logoCandidates = [
        'image/wdatransparent.png',
        'wdatransparent.png',
        'image/wdalogo.png',
        'wdalogo.png',
    ];
    $logoPath = collect($logoCandidates)
        ->first(fn (string $path): bool => file_exists(public_path($path)));
    $hasLogo = filled($logoPath);
    $brandTitle = 'Water District Asuncion - HRIS';
@endphp

<div class="wda-auth-page">
    <style>
        .wda-auth-page {
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            background: #eef7ff;
        }

        .wda-auth-card {
            width: min(920px, 100%);
            min-height: 500px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, .95fr);
            overflow: hidden;
            border: 0;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 28px 74px rgba(8, 47, 73, .22);
        }

        .wda-auth-form,
        .wda-auth-side {
            min-height: 500px;
        }

        .wda-auth-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: clamp(36px, 5vw, 60px);
            background: #ffffff;
        }

        .wda-auth-heading,
        .wda-auth-content {
            width: min(100%, 360px);
        }

        .wda-auth-heading {
            margin: 0 0 28px;
            text-align: center;
            color: #082f49 !important;
            font-size: 30px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: 0;
        }

        .wda-mobile-brand,
        .wda-mobile-subtitle {
            display: none;
        }

        .wda-auth-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: clamp(38px, 5vw, 56px);
            text-align: center;
            background:
                radial-gradient(circle at 50% 28%, rgba(255, 255, 255, .2), transparent 34%),
                linear-gradient(145deg, #075985 0%, #0284c7 45%, #38bdf8 100%);
        }

        .wda-auth-logo-wrap {
            width: 156px;
            height: 156px;
            display: grid;
            place-items: center;
            margin-bottom: 26px;
            border-radius: 999px;
            background: #ffffff;
            box-shadow: 0 24px 44px rgba(8, 47, 73, .24);
        }

        .wda-auth-logo {
            width: 112px;
            height: 112px;
            display: block;
            object-fit: contain;
            object-position: center;
        }

        .wda-auth-side h2 {
            margin: 0 0 8px;
            color: #ffffff;
            font-size: clamp(20px, 2.5vw, 28px);
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: 0;
        }

        .wda-auth-side p {
            max-width: 280px;
            margin: 0;
            color: #e0f2fe;
            font-size: 14px;
            line-height: 1.55;
            font-weight: 600;
        }

        .wda-auth-form label,
        .wda-auth-form label span,
        .wda-auth-form .fi-label,
        .wda-auth-form .fi-label span,
        .wda-auth-form .fi-fo-field-wrp-label,
        .wda-auth-form .fi-fo-field-wrp-label span,
        .wda-auth-form .fi-fo-field-wrp-label label,
        .wda-auth-form .fi-fo-field-wrp-helper-text,
        .wda-auth-form .fi-fo-field-wrp-hint,
        .wda-auth-form .fi-checkbox-label,
        .wda-auth-form .fi-fo-checkbox-list-option-label {
            color: #082f49 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .wda-auth-form .fi-form,
        .wda-auth-form form {
            width: 100%;
        }

        .wda-auth-form .fi-form {
            gap: 18px;
        }

        .wda-auth-form .fi-fo-field-wrp-label {
            display: flex !important;
        }

        .wda-auth-form input,
        .wda-auth-form .fi-input,
        .wda-auth-form .fi-input-wrp input,
        .wda-auth-form input[type="text"],
        .wda-auth-form input[type="email"],
        .wda-auth-form input[type="password"] {
            min-height: 44px;
            color: #082f49 !important;
            background-color: #ffffff !important;
            -webkit-text-fill-color: #082f49 !important;
            font-weight: 700;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .wda-auth-form .fi-input-wrp,
        .wda-auth-form .fi-input-wrp-input {
            background-color: #ffffff !important;
            border: 1px solid #bae6fd !important;
            border-radius: 12px !important;
            color: #082f49 !important;
            box-shadow: none !important;
        }

        .wda-auth-form input::placeholder {
            color: #64748b !important;
            opacity: 1 !important;
        }

        .wda-auth-form .fi-input-wrp:focus-within {
            border-color: #0284c7 !important;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, .18) !important;
        }

        .wda-auth-form svg {
            color: #0284c7 !important;
        }

        .wda-auth-form .fi-fo-field-wrp-error-message,
        .wda-auth-form .fi-fo-field-wrp-error-message *,
        .wda-auth-form .fi-invalid,
        .wda-auth-form .fi-invalid *,
        .wda-auth-form [data-validation-error],
        .wda-auth-form [data-validation-error] *,
        .wda-auth-form [role="alert"],
        .wda-auth-form [role="alert"] * {
            color: #dc2626 !important;
            -webkit-text-fill-color: #dc2626 !important;
        }

        .wda-auth-form .fi-checkbox,
        .wda-auth-form .fi-checkbox-list,
        .wda-auth-form .fi-fo-checkbox-list,
        .wda-auth-form .fi-checkbox-list-option,
        .wda-auth-form .fi-fo-checkbox-list-option,
        .wda-auth-form label:has(input[type="checkbox"]) {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .wda-auth-form .fi-checkbox-input,
        .wda-auth-form input[type="checkbox"] {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            margin: 0 !important;
            flex: 0 0 16px !important;
            color: #0284c7 !important;
            background-color: #ffffff !important;
            border-color: #7dd3fc !important;
        }

        .wda-auth-form input[type="checkbox"]:checked {
            background-color: #0284c7 !important;
            border-color: #0284c7 !important;
        }

        .wda-auth-form .fi-checkbox-label,
        .wda-auth-form input[type="checkbox"] + span {
            display: inline !important;
            margin: 0 !important;
            line-height: 1.3 !important;
        }

        .wda-auth-form .fi-ac {
            justify-content: stretch;
        }

        .wda-auth-form button[type="submit"],
        .wda-auth-form .fi-btn[type="submit"] {
            width: 100%;
            min-height: 46px;
            border: 0 !important;
            border-radius: 12px !important;
            background: #0284c7 !important;
            color: #ffffff !important;
            font-weight: 900 !important;
            box-shadow: none !important;
        }

        .wda-auth-form button[type="submit"] *,
        .wda-auth-form .fi-btn[type="submit"] * {
            color: #ffffff !important;
        }

        .wda-auth-form button[type="submit"]:hover,
        .wda-auth-form .fi-btn[type="submit"]:hover {
            background: #0369a1 !important;
            color: #ffffff !important;
        }

        .dark .wda-auth-card,
        .dark .wda-auth-form {
            border: 0 !important;
        }

        @media (max-width: 768px) {
            .wda-auth-page {
                align-items: center;
                padding: 18px;
            }

            .wda-auth-card {
                grid-template-columns: 1fr;
                min-height: auto;
                border-radius: 22px;
            }

            .wda-auth-form {
                min-height: auto;
                padding: 38px 26px;
                background:
                    radial-gradient(circle at top left, rgba(255, 255, 255, .22), transparent 38%),
                    linear-gradient(135deg, #075985 0%, #0284c7 54%, #38bdf8 100%) !important;
            }

            .wda-auth-heading {
                margin-bottom: 28px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
                color: #ffffff !important;
                text-align: center;
            }

            .wda-auth-login-title {
                display: none;
            }

            .wda-auth-heading.has-logo::before {
                content: "";
                width: 92px;
                height: 92px;
                display: block;
                border-radius: 999px;
                background-color: #ffffff;
                background-image: url("{{ $hasLogo ? asset($logoPath) : '' }}");
                background-size: 68px 68px;
                background-position: center;
                background-repeat: no-repeat;
                box-shadow: 0 16px 34px rgba(8, 47, 73, .2);
            }

            .wda-mobile-brand {
                display: block;
                color: #ffffff !important;
                font-size: 15px;
                line-height: 1.2;
                font-weight: 900;
                text-align: center;
            }

            .wda-mobile-subtitle {
                display: block;
                color: #e0f2fe !important;
                font-size: 13px;
                line-height: 1.4;
                font-weight: 600;
                text-align: center;
            }

            .wda-auth-side {
                display: none;
            }

            .wda-auth-form label,
            .wda-auth-form label span,
            .wda-auth-form .fi-label,
            .wda-auth-form .fi-label span,
            .wda-auth-form .fi-fo-field-wrp-label,
            .wda-auth-form .fi-fo-field-wrp-label span,
            .wda-auth-form .fi-checkbox-label {
                color: #ffffff !important;
            }
        }
    </style>

    <main class="wda-auth-card">
        <section class="wda-auth-form">
            <h1 @class(['wda-auth-heading', 'has-logo' => $hasLogo])>
                <span class="wda-auth-login-title">Login</span>
                <span class="wda-mobile-brand">{{ $brandTitle }}</span>
                <span class="wda-mobile-subtitle">Human Resource Information System</span>
            </h1>

            <div class="wda-auth-content">
                {{ $this->content }}
            </div>
        </section>

        <aside class="wda-auth-side">
            @if ($hasLogo)
                <div class="wda-auth-logo-wrap">
                    <img
                        src="{{ asset($logoPath) }}"
                        alt="Asuncion Water District"
                        class="wda-auth-logo"
                    >
                </div>
            @endif

            <h2>{{ $brandTitle }}</h2>

            <p>Human Resource Information System</p>
        </aside>
    </main>

    <x-filament-actions::modals />
</div>
