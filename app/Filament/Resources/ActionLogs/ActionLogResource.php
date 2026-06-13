<?php

namespace App\Filament\Resources\ActionLogs;

use App\Filament\Resources\ActionLogs\Pages\ListActionLogs;
use App\Filament\Resources\ActionLogs\Tables\ActionLogsTable;
use App\Filament\Resources\Concerns\AuthorizesResourceShieldPermissions;
use App\Models\ActionLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ActionLogResource extends Resource
{
    use AuthorizesResourceShieldPermissions;

    protected static ?string $model = ActionLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Actions Log';

    protected static ?string $modelLabel = 'Action Log';

    protected static ?string $pluralModelLabel = 'Actions Log';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'actions-log';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return ActionLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActionLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
