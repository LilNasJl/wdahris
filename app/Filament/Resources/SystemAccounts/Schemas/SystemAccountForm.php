<?php

namespace App\Filament\Resources\SystemAccounts\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class SystemAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->schema([
                        TextInput::make('username')
                            ->placeholder('e.g., hradmin')
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: User::class,
                                column: 'username',
                                ignoreRecord: true,
                            )
                            ->validationAttribute('username')
                            ->validationMessages([
                                'unique' => 'This username is already registered.',
                            ]),

                        TextInput::make('email')
                            ->placeholder('e.g., hradmin@example.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: User::class,
                                column: 'email',
                                ignoreRecord: true,
                            )
                            ->validationAttribute('email address')
                            ->validationMessages([
                                'unique' => 'This email address is already registered.',
                            ]),

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->placeholder('Leave empty to keep current password')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),

                        Select::make('roles')
                            ->label('Roles')
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('guard_name', 'web')
                                    ->where('name', '!=', 'employee')
                                    ->orderBy('name'),
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record): string => (string) str($record->name)->replace('_', ' ')->title())
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
