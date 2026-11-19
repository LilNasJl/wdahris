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
    $brandTitle = 'Water District Asuncion';

    // Carousel slides requested by user
    $slides = [
        [
            'image' => asset('image/hris-carousel-self-service.png'),
            'alt' => 'HRIS Employee Self-Service',
        ],
        [
            'image' => asset('image/hris-carousel-payroll.png'),
            'alt' => 'HRIS Payroll and Compensation',
        ],
        [
            'image' => asset('image/hris-carousel-people.png'),
            'alt' => 'HRIS People and Records Management',
        ],
    ];
@endphp

<div class="wda-login-layout">
    <style>
        /* Base Viewport & Background: Full blue wave on desktop AND mobile */
        .wda-login-layout {
            min-height: 100vh;
            min-height: 100svh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background-color: #0b347a;
            background-image: url("{{ asset('image/login-bg.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            overflow-x: hidden;
            box-sizing: border-box;
            font-family: inherit;
            padding: 24px 16px;
        }

        @media (min-width: 1024px) {
            .wda-login-layout {
                padding: 40px 48px;
            }
        }

        /* Main Container: Two-column grid on desktop, centered on mobile */
        .wda-main-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 32px;
            margin: auto;
            position: relative;
            z-index: 10;
        }

        @media (min-width: 1024px) {
            .wda-main-container {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 56px;
            }
        }

        @media (min-width: 1280px) {
            .wda-main-container {
                gap: 80px;
            }
        }

        /* LEFT: The Floating White Login Card */
        .wda-login-card {
            width: 100%;
            max-width: 410px;
            background: #ffffff;
            border-radius: 22px;
            padding: 30px 24px 24px 24px;
            box-shadow: 0 25px 60px -10px rgba(2, 20, 60, 0.42), 0 0 0 1px rgba(255, 255, 255, 0.2);
            box-sizing: border-box;
            position: relative;
            z-index: 20;
            flex-shrink: 0;
        }

        @media (min-width: 768px) {
            .wda-login-card {
                padding: 36px 32px 28px 32px;
                max-width: 430px;
            }
        }

        @media (min-width: 1024px) {
            .wda-login-card {
                padding: 40px 36px 30px 36px;
                max-width: 440px;
            }
        }

        /* Header Logo & Brand Row */
        .wda-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .wda-logo {
            height: 44px !important;
            max-height: 44px !important;
            width: 44px !important;
            max-width: 44px !important;
            object-fit: contain !important;
            flex-shrink: 0 !important;
            display: block !important;
        }

        .wda-header-divider {
            height: 32px;
            width: 1.5px;
            background-color: #e2e8f0;
            flex-shrink: 0;
        }

        .wda-header-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .wda-brand-name {
            font-size: 14.5px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.015em;
        }

        .wda-brand-tag-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 3px;
        }

        .wda-hris-badge {
            display: inline-block;
            padding: 1px 5px;
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            border-radius: 4px;
            background-color: #f0f7ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            letter-spacing: 0.05em;
            line-height: 1.2;
        }

        .wda-hris-desc {
            font-size: 9.5px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1.2;
        }

        /* Titles */
        .wda-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 9999px;
            background: #f0f7ff;
            border: 1px solid #dbeafe;
            color: #0284c7;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .wda-pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: #0284c7;
        }

        .wda-form-heading {
            font-size: 25px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.025em;
            line-height: 1.15;
            margin: 0 0 6px 0;
        }

        .wda-form-subtitle {
            font-size: 13px;
            color: #64748b;
            font-weight: 400;
            margin: 0 0 20px 0;
            line-height: 1.45;
        }

        /* Form Controls & Layout Fixes */
        .wda-form-container form,
        .wda-form-container .fi-form {
            display: flex;
            flex-direction: column;
            gap: 15px !important;
            width: 100%;
        }

        .wda-form-container .fi-fo-field-wrp {
            margin-bottom: 0 !important;
        }

        .wda-form-container .fi-fo-field-label-col,
        .wda-form-container .fi-fo-field-label-ctn {
            margin-bottom: 5px !important;
            display: flex !important;
            align-items: center !important;
        }

        /* Fix floating asterisk bug: keep label and asterisk strictly inline */
        .wda-form-container .fi-fo-field-label,
        .wda-form-container label {
            display: inline-flex !important;
            align-items: baseline !important;
            gap: 3px !important;
            margin-bottom: 0 !important;
        }

        .wda-form-container .fi-fo-field-label-content {
            display: inline-flex !important;
            align-items: baseline !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            line-height: 1.2 !important;
        }

        .wda-form-container sup.fi-fo-field-label-required-mark {
            display: inline-block !important;
            position: static !important;
            vertical-align: baseline !important;
            font-size: 14px !important;
            font-weight: 800 !important;
            color: #ef4444 !important;
            line-height: 1 !important;
            margin-left: 2px !important;
            top: 0 !important;
        }

        /* Input Container */
        .wda-form-container .fi-input-wrp {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
            transition: all 0.15s ease-in-out !important;
            min-height: 44px !important;
            display: flex !important;
            align-items: center !important;
        }

        .wda-form-container .fi-input-wrp:focus-within {
            border-color: #1d68d8 !important;
            box-shadow: 0 0 0 3px rgba(29, 104, 216, 0.16) !important;
        }

        .wda-form-container input[type="text"],
        .wda-form-container input[type="password"] {
            min-height: 42px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 8px 12px !important;
            width: 100% !important;
        }

        .wda-form-container input::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
        }

        .wda-form-container .fi-input-wrp-prefix,
        .wda-form-container .fi-input-wrp-suffix {
            padding: 0 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .wda-form-container .fi-input-wrp-prefix svg,
        .wda-form-container .fi-input-wrp-suffix svg {
            color: #64748b !important;
            width: 17px !important;
            height: 17px !important;
        }

        /* Checkbox */
        .wda-form-container label:has(input[type="checkbox"]) {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            cursor: pointer !important;
        }

        .wda-form-container input[type="checkbox"] {
            width: 16px !important;
            height: 16px !important;
            border-radius: 4px !important;
            border: 1.5px solid #94a3b8 !important;
            color: #1d68d8 !important;
            margin: 0 !important;
            cursor: pointer !important;
        }

        .wda-form-container input[type="checkbox"]:checked {
            background-color: #1d68d8 !important;
            border-color: #1d68d8 !important;
        }

        .wda-form-container .fi-checkbox-label {
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #475569 !important;
            margin: 0 !important;
            cursor: pointer !important;
        }

        /* Submit Button */
        .wda-form-container button[type="submit"],
        .wda-form-container .fi-btn[type="submit"] {
            width: 100% !important;
            min-height: 46px !important;
            background: #1d68d8 !important;
            color: #ffffff !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(29, 104, 216, 0.28) !important;
            transition: all 0.15s ease-in-out !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            cursor: pointer !important;
            margin-top: 4px !important;
        }

        .wda-form-container button[type="submit"]:hover,
        .wda-form-container .fi-btn[type="submit"]:hover {
            background: #1558b8 !important;
            box-shadow: 0 6px 16px rgba(29, 104, 216, 0.35) !important;
            transform: translateY(-1px);
        }

        .wda-form-container button[type="submit"]:active,
        .wda-form-container .fi-btn[type="submit"]:active {
            transform: translateY(0);
        }

        .wda-form-container button[type="submit"] *,
        .wda-form-container .fi-btn[type="submit"] * {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        /* Footnotes */
        .wda-security-footnote {
            margin-top: 18px;
            font-size: 11.5px;
            color: #94a3b8;
            font-weight: 500;
            text-align: center;
            line-height: 1.5;
        }

        .wda-security-icon {
            display: inline-block !important;
            width: 14px !important;
            height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            color: #94a3b8;
            vertical-align: -2px !important;
            margin-right: 5px;
        }

        .wda-footer {
            font-size: 11.5px;
            color: #94a3b8;
            font-weight: 400;
            text-align: center;
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        /* RIGHT: Showcase & Carousel (Visible on Desktop) */
        .wda-right-panel {
            display: none;
        }

        @media (min-width: 1024px) {
            .wda-right-panel {
                display: flex;
                flex: 1;
                flex-direction: column;
                justify-content: center;
                align-items: flex-start;
                max-width: 520px;
                position: relative;
                z-index: 10;
            }
        }

        .wda-glass-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.32);
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .wda-glass-pill-icon {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            color: #bae6fd;
            flex-shrink: 0;
        }

        .wda-right-heading {
            font-size: clamp(32px, 2.8vw, 44px);
            font-weight: 900;
            color: #ffffff;
            line-height: 1.15;
            letter-spacing: -0.025em;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 10px rgba(0, 20, 60, 0.25);
        }

        .wda-right-desc {
            font-size: 14px;
            line-height: 1.5;
            color: #e0f2fe;
            font-weight: 400;
            margin: 0 0 22px 0;
            max-width: 480px;
            text-shadow: 0 1px 4px rgba(0, 20, 60, 0.15);
        }

        /* Showcase Carousel Card */
        .wda-carousel-card {
            width: 100%;
            border-radius: 18px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 24px 48px -12px rgba(2, 20, 60, 0.45);
            position: relative;
            aspect-ratio: 1570 / 1001;
        }

        .wda-carousel-slide {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .wda-carousel-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            border-radius: 18px;
        }

        /* Controls Bar below card */
        .wda-carousel-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-top: 14px;
            padding: 0 4px;
            box-sizing: border-box;
        }

        .wda-nav-btn-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .wda-nav-btn {
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
        }

        .wda-nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.55);
            transform: scale(1.05);
        }

        .wda-nav-btn svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            stroke-width: 2.5;
        }

        .wda-indicators {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .wda-dot {
            height: 6px;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .wda-dot-active {
            width: 24px;
            background: #ffffff;
        }

        .wda-dot-inactive {
            width: 6px;
            background: rgba(255, 255, 255, 0.4);
        }

        .wda-dot-inactive:hover {
            background: rgba(255, 255, 255, 0.7);
        }
    </style>

    <div class="wda-main-container">
        <!-- LEFT: Floating White Login Card (Cleanly placed on top of blue wave background) -->
        <div class="wda-login-card">
            <!-- Header Logo & Brand Row -->
            <header class="wda-header">
                @if ($hasLogo)
                    <img
                        src="{{ asset($logoPath) }}"
                        alt="Water District Asuncion"
                        class="wda-logo"
                    >
                    <div class="wda-header-divider"></div>
                @endif
                <div class="wda-header-text">
                    <span class="wda-brand-name">{{ $brandTitle }}</span>
                    <div class="wda-brand-tag-row">
                        <span class="wda-hris-badge">HRIS</span>
                        <span class="wda-hris-desc">Human Resource Information System</span>
                    </div>
                </div>
            </header>

            <!-- Heading & Subtitle -->
            <h1 class="wda-form-heading">
                Sign in to your account
            </h1>
            <p class="wda-form-subtitle">
                Use your HR username or employee Company ID.
            </p>

            <!-- Filament Form -->
            <div class="wda-form-container">
                {{ $this->content }}
            </div>

            <!-- Security Footnote -->
            <div class="wda-security-footnote">
                <svg class="wda-security-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                <span>Authorized access for Water District Asuncion personnel</span>
            </div>

            <!-- Footer -->
            <footer class="wda-footer">
                &copy; {{ date('Y') }} {{ $brandTitle }}
            </footer>
        </div>

        <!-- RIGHT: Blue Wave Showcase & Carousel -->
        <div class="wda-right-panel" x-data="{
            activeSlide: 0,
            slides: {{ Js::from($slides) }},
            timer: null,
            init() {
                this.timer = setInterval(() => { this.next() }, 6000);
            },
            prev() {
                this.activeSlide = (this.activeSlide === 0) ? this.slides.length - 1 : this.activeSlide - 1;
            },
            next() {
                this.activeSlide = (this.activeSlide === this.slides.length - 1) ? 0 : this.activeSlide + 1;
            }
        }">
            <!-- Top Glass Pill -->
            <div class="wda-glass-pill">
                <svg class="wda-glass-pill-icon" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </svg>
                <span>CONNECTED WORKFORCE OPERATIONS</span>
            </div>

            <!-- Hero Titles -->
            <h2 class="wda-right-heading">
                Human Resource<br>
                Information System
            </h2>

            <!-- Featured Showcase Card with Carousel -->
            <div class="wda-carousel-card">
                <template x-for="(slide, index) in slides" :key="index">
                    <div
                        x-show="activeSlide === index"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="wda-carousel-slide"
                    >
                        <img
                            :src="slide.image"
                            :alt="slide.alt"
                            class="wda-carousel-img"
                        >
                    </div>
                </template>
            </div>

            <!-- Carousel Navigation Controls & Indicators -->
            <div class="wda-carousel-controls" x-show="slides.length > 1">
                <div class="wda-nav-btn-group">
                    <button
                        type="button"
                        @click="prev()"
                        aria-label="Previous slide"
                        class="wda-nav-btn"
                    >
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="next()"
                        aria-label="Next slide"
                        class="wda-nav-btn"
                    >
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>

                <div class="wda-indicators">
                    <template x-for="(slide, index) in slides" :key="'dot-' + index">
                        <button
                            type="button"
                            @click="activeSlide = index"
                            class="wda-dot"
                            :class="activeSlide === index ? 'wda-dot-active' : 'wda-dot-inactive'"
                            :aria-label="'Go to slide ' + (index + 1)"
                        ></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />
</div>
