<x-filament-panels::page>
    <style>
        /* =====================================================================
           Personnel Dashboard Widget Theme Overrides
           Deep Ocean / Royal Blue theme consistent with HRIS Design System
           ===================================================================== */

        /* 1. Stats Overview Widget Cards */
        .fi-wi-stats-overview-stat {
            background: linear-gradient(145deg, #0b172a 0%, #0f223d 100%) !important;
            border: 1px solid rgba(56, 189, 248, 0.2) !important;
            border-radius: 14px !important;
            box-shadow: 0 4px 18px -2px rgba(2, 6, 23, 0.45) !important;
            padding: 18px 20px !important;
            transition: all 0.2s ease !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .fi-wi-stats-overview-stat:hover {
            border-color: rgba(56, 189, 248, 0.45) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -4px rgba(2, 132, 199, 0.25) !important;
        }

        .fi-wi-stats-overview-stat-label {
            font-size: 11px !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
            color: #94a3b8 !important;
        }

        .fi-wi-stats-overview-stat-value {
            font-size: 28px !important;
            font-weight: 800 !important;
            color: #ffffff !important;
            letter-spacing: -0.02em !important;
            margin: 4px 0 !important;
        }

        .fi-wi-stats-overview-stat-description {
            font-size: 11.5px !important;
            color: #94a3b8 !important;
        }

        /* 2. Tables Widget Container */
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

        /* Table Header Cells */
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

        /* Empty state styling */
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
</x-filament-panels::page>
