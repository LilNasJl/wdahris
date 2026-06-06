<?php

namespace App\Filament\Resources\SystemAccounts;

use App\Filament\Resources\Concerns\AuthorizesResourceShieldPermissions;
use App\Filament\Resources\SystemAccounts\Pages\CreateSystemAccount;
use App\Filament\Resources\SystemAccounts\Pages\EditSystemAccount;
use App\Filament\Resources\SystemAccounts\Pages\ListSystemAccounts;
use App\Filament\Resources\SystemAccounts\Schemas\SystemAccountForm;
use App\Filament\Resources\SystemAccounts\Tables\SystemAccountsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SystemAccountResource extends Resource
{
    use AuthorizesResourceShieldPermissions;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserCircle;

    protected static string|UnitEnum|null $navigationGroup = 'ACCOUNT MANAGEMENT';

    protected static ?string $navigationLabel = 'System Accounts';

    protected static ?string $modelLabel = 'System Account';

    protected static ?string $pluralModelLabel = 'System Accounts';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'system-accounts';

    public static function form(Schema $schema): Schema
    {
        return SystemAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemAccounts::route('/'),
            'create' => CreateSystemAccount::route('/create'),
            'edit' => EditSystemAccount::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->where('username', '!=', 'masteradmin')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
