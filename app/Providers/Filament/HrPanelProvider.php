<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HrPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('hr')
            ->path('hr')
            ->brandName('Water District Asuncion - HRIS')
            ->brandLogo(asset('image/wdatransparent.png'))
            ->brandLogoHeight('42px')
            ->globalSearch(false)
            ->favicon(asset('image/wdatransparent.png'))
            ->databaseNotifications()
            ->login(Login::class)
            ->navigationGroups([

                NavigationGroup::make()
                    ->label('201 Records'),

                NavigationGroup::make()
                    ->label('History'),

                NavigationGroup::make()
                    ->label('Organization Setup'),

                NavigationGroup::make()
                    ->label('Compliance & Benefits'),

                NavigationGroup::make()
                    ->label('Settings'),

                NavigationGroup::make()
                    ->label('ACCOUNT MANAGEMENT'),
            ])
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([

            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('ACCOUNT MANAGEMENT')
                    ->navigationLabel('Filament Roles Page')
                    ->navigationSort(1),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->resourceEditPageRedirect('index')
            ->resourceCreatePageRedirect('index')
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
