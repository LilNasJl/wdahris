@php
    $brandName = filament()->getBrandName();
    $brandLogo = filament()->getBrandLogo();
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '1.5rem';
    $darkModeBrandLogo = filament()->getDarkModeBrandLogo();
    $hasDarkModeBrandLogo = filled($darkModeBrandLogo);

    $getLogoClasses = fn (bool $isDarkMode): string => \Illuminate\Support\Arr::toCssClasses([
        'fi-logo',
        'fi-logo-light' => $hasDarkModeBrandLogo && (! $isDarkMode),
        'fi-logo-dark' => $isDarkMode,
    ]);

    $logoStyles = "height: {$brandLogoHeight}";
@endphp

@capture($content, $logo, $isDarkMode = false)
    @if ($logo instanceof \Illuminate\Contracts\Support\Htmlable)
        <div
            {{
                $attributes
                    ->class([$getLogoClasses($isDarkMode)])
                    ->style([$logoStyles])
            }}
        >
            {{ $logo }}
        </div>
    @elseif (filled($logo))
        <div class="fi-logo-wrapper" style="display: inline-flex; align-items: center; gap: 11px; text-decoration: none;">
            <img
                alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}"
                src="{{ $logo }}"
                {{
                    $attributes
                        ->class([$getLogoClasses($isDarkMode)])
                        ->style([$logoStyles])
                }}
            />
            <div class="fi-logo-text" style="display: flex; flex-direction: column; justify-content: center; text-align: left; line-height: 1.25;">
                <span class="fi-logo-title text-gray-950 dark:text-white" style="font-size: 14.5px; font-weight: 800; letter-spacing: -0.015em; white-space: nowrap;">
                    Human Resource Management System
                </span>
                <span class="fi-logo-subtitle text-sky-600 dark:text-sky-400" style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; white-space: nowrap;">
                    Water District Asuncion
                </span>
            </div>
        </div>
    @else
        <div
            {{
                $attributes->class([
                    $getLogoClasses($isDarkMode),
                ])
            }}
        >
            {{ $brandName }}
        </div>
    @endif
@endcapture

{{ $content($brandLogo) }}

@if ($hasDarkModeBrandLogo)
    {{ $content($darkModeBrandLogo, isDarkMode: true) }}
@endif
