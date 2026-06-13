<?php

namespace App\Filament\Pages;

use App\Services\DatabaseBackupService;
use App\Services\DatabaseMaintenanceService;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class DatabaseManagement extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.database-management';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CircleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Database Management';

    protected static ?string $title = 'Database Management';

    protected static ?int $navigationSort = 1;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadBackup')
                ->label('Download Backup')
                ->icon(Heroicon::ArrowDownTray)
                ->action(fn (DatabaseBackupService $backup): StreamedResponse => $backup->download()),

            Action::make('clearDatabase')
                ->label('Clear Database')
                ->icon(Heroicon::Trash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear database records?')
                ->modalDescription('This clears operational records while preserving system accounts, roles, permissions, sessions, migrations, and audit logs.')
                ->modalSubmitActionLabel('Clear Database')
                ->action(function (DatabaseMaintenanceService $maintenance): void {
                    $result = $maintenance->clearOperationalData();

                    Notification::make()
                        ->success()
                        ->title('Database cleared')
                        ->body($result['deleted_records'].' records cleared from '.count($result['tables']).' tables.')
                        ->send();
                }),
        ];
    }
}
