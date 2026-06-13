<?php

namespace App\Filament\Resources\AccountLogs;

use App\Filament\Resources\AccountLogs\Pages\ListAccountLogs;
use App\Filament\Resources\AccountLogs\Tables\AccountLogsTable;
use App\Filament\Resources\Concerns\AuthorizesResourceShieldPermissions;
use App\Models\AccountLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AccountLogResource extends Resource
{
    use AuthorizesResourceShieldPermissions;

    protected static ?string $model = AccountLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Accounts Log';

    protected static ?string $modelLabel = 'Account Log';

    protected static ?string $pluralModelLabel = 'Accounts Log';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'accounts-log';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return AccountLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountLogs::route('/'),
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
