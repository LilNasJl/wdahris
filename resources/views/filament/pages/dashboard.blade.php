<x-filament-panels::page>
    <style>
        /* =====================================================================
           WDA HRIS - Modern Executive Dashboard Styles (Dark Mode Default)
           Color Scheme: Deep Ocean & Royal Blues (#0b1f3d, #1d68d8, #38bdf8)
           ===================================================================== */

        /* -------------------------------------------------------------
           1. HERO BANNER
           ------------------------------------------------------------- */
        .wda-dashboard-hero {
            background: linear-gradient(135deg, #0b1f3d 0%, #102e5b 50%, #0d2448 100%) !important;
            border: 1px solid rgba(56, 189, 248, 0.25) !important;
            border-radius: 16px !important;
            padding: 22px 28px !important;
            margin-bottom: 22px !important;
            box-shadow: 0 10px 30px -5px rgba(2, 20, 60, 0.4), 0 0 0 1px rgba(56, 189, 248, 0.1) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .wda-dashboard-hero::before {
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
            max-width: 600px;
        }

        .wda-hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 9999px;
            background: rgba(56, 189, 248, 0.15) !important;
            border: 1px solid rgba(56, 189, 248, 0.35) !important;
            color: #bae6fd !important;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .wda-hero-pill-icon {
            width: 14px !important;
            height: 14px !important;
            color: #38bdf8 !important;
            flex-shrink: 0;
        }

        .wda-hero-title {
            font-size: clamp(22px, 2.2vw, 28px);
            font-weight: 800;
            color: #ffffff !important;
            letter-spacing: -0.025em;
            margin: 0 0 6px 0;
            line-height: 1.2;
            text-shadow: 0 2px 10px rgba(0, 20, 60, 0.3) !important;
        }

        .wda-hero-subtitle {
            font-size: 13.5px;
            color: #93c5fd !important;
            margin: 0;
            font-weight: 400;
            line-height: 1.45;
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
            background: rgba(15, 23, 42, 0.65) !important;
            border: 1px solid rgba(56, 189, 248, 0.22) !important;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 600;
            color: #e2e8f0 !important;
            backdrop-filter: blur(8px);
        }

        .wda-meta-icon {
            width: 16px !important;
            height: 16px !important;
            color: #38bdf8 !important;
            flex-shrink: 0;
        }

        .wda-hero-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 15px;
            background: rgba(16, 185, 129, 0.14) !important;
            border: 1px solid rgba(16, 185, 129, 0.35) !important;
            border-radius: 10px;
            font-size: 12.5px;
            font-weight: 700;
            color: #34d399 !important;
            backdrop-filter: blur(8px);
        }

        .wda-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981 !important;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: wda-pulse 2s infinite;
        }

        @keyframes wda-pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* -------------------------------------------------------------
           2. STATS OVERVIEW CARDS
           ------------------------------------------------------------- */
        .fi-wi-stats-overview {
            margin-bottom: 22px !important;
        }

        .fi-wi-stats-overview-stat {
            background: linear-gradient(145deg, #0b172a 0%, #0f223d 100%) !important;
            border: 1px solid rgba(56, 189, 248, 0.18) !important;
            border-radius: 14px !important;
            padding: 18px 20px !important;
            box-shadow: 0 4px 18px -2px rgba(2, 6, 23, 0.5) !important;
            position: relative !important;
            overflow: hidden !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Colored top stripes */
        .fi-wi-stats-overview-stat::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3.5px;
            background: linear-gradient(90deg, #1d68d8, #38bdf8);
        }

        .wda-stat-permanent::before {
            background: linear-gradient(90deg, #059669, #34d399) !important;
        }

        .wda-stat-casual::before {
            background: linear-gradient(90deg, #d97706, #fbbf24) !important;
        }

        .wda-stat-job-order::before {
            background: linear-gradient(90deg, #0284c7, #38bdf8) !important;
        }

        .wda-stat-temporary::before {
            background: linear-gradient(90deg, #6366f1, #a5b4fc) !important;
        }

        .wda-stat-coterminous::before {
            background: linear-gradient(90deg, #e11d48, #fb7185) !important;
        }

        .fi-wi-stats-overview-stat:hover {
            transform: translateY(-3px) !important;
            border-color: rgba(56, 189, 248, 0.45) !important;
            box-shadow: 0 12px 28px -4px rgba(2, 132, 199, 0.25), 0 0 0 1px rgba(56, 189, 248, 0.2) !important;
        }

        .fi-wi-stats-overview-stat-label-ctn {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            margin-bottom: 8px !important;
        }

        .fi-wi-stats-overview-stat-label-ctn svg {
            width: 17px !important;
            height: 17px !important;
            color: #38bdf8 !important;
            filter: drop-shadow(0 2px 4px rgba(56, 189, 248, 0.3));
        }

        .wda-stat-permanent .fi-wi-stats-overview-stat-label-ctn svg { color: #34d399 !important; }
        .wda-stat-casual .fi-wi-stats-overview-stat-label-ctn svg { color: #fbbf24 !important; }
        .wda-stat-job-order .fi-wi-stats-overview-stat-label-ctn svg { color: #38bdf8 !important; }
        .wda-stat-temporary .fi-wi-stats-overview-stat-label-ctn svg { color: #a5b4fc !important; }
        .wda-stat-coterminous .fi-wi-stats-overview-stat-label-ctn svg { color: #fb7185 !important; }

        .fi-wi-stats-overview-stat-label {
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
            color: #94a3b8 !important;
        }

        .fi-wi-stats-overview-stat-value {
            font-size: 32px !important;
            font-weight: 800 !important;
            line-height: 1.1 !important;
            margin-bottom: 4px !important;
        }

        .wda-stat-total-active .fi-wi-stats-overview-stat-value { color: #38bdf8 !important; }
        .wda-stat-permanent .fi-wi-stats-overview-stat-value { color: #34d399 !important; }
        .wda-stat-casual .fi-wi-stats-overview-stat-value { color: #fbbf24 !important; }
        .wda-stat-job-order .fi-wi-stats-overview-stat-value { color: #38bdf8 !important; }
        .wda-stat-temporary .fi-wi-stats-overview-stat-value { color: #a5b4fc !important; }
        .wda-stat-coterminous .fi-wi-stats-overview-stat-value { color: #fb7185 !important; }

        .fi-wi-stats-overview-stat-description {
            font-size: 11px !important;
            color: #94a3b8 !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        /* -------------------------------------------------------------
           3. TABLES & CHART WIDGETS
           ------------------------------------------------------------- */
        .fi-wi-table > .fi-ta-ctn,
        .fi-wi-chart .fi-section {
            background: linear-gradient(145deg, #0b172a 0%, #0f223d 100%) !important;
            border: 1px solid rgba(56, 189, 248, 0.18) !important;
            border-radius: 14px !important;
            box-shadow: 0 4px 18px -2px rgba(2, 6, 23, 0.45) !important;
            overflow: hidden !important;
        }

        .fi-ta-header,
        .fi-wi-chart .fi-section-header {
            background: rgba(14, 28, 49, 0.75) !important;
            border-bottom: 1px solid rgba(56, 189, 248, 0.15) !important;
            padding: 14px 20px !important;
        }

        .fi-ta-header-heading,
        .fi-wi-chart .fi-section-header-heading {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            letter-spacing: -0.01em !important;
        }

        /* Table header row cells */
        .fi-ta-header-cell {
            background: rgba(11, 23, 42, 0.85) !important;
            font-size: 10.5px !important;
            font-weight: 700 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            color: #7dd3fc !important;
            border-bottom: 1px solid rgba(56, 189, 248, 0.14) !important;
            padding-top: 10px !important;
            padding-bottom: 10px !important;
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
        }

        /* Empty states */
        .fi-ta-empty-state-icon-ctn {
            background: rgba(56, 189, 248, 0.1) !important;
            border-radius: 9999px !important;
            padding: 12px !important;
            border: none !important;
        }

        .fi-ta-empty-state-icon-ctn svg {
            color: #38bdf8 !important;
            width: 32px !important;
            height: 32px !important;
        }

        .fi-ta-empty-state-heading {
            color: #f1f5f9 !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin-top: 10px !important;
        }

        .fi-ta-empty-state-description {
            color: #94a3b8 !important;
            font-size: 12.5px !important;
            max-width: 340px !important;
            margin-inline: auto !important;
        }
    </style>
</x-filament-panels::page>
