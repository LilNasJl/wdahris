<?php

namespace App\Filament\Resources\AccountLogs\Tables;

use App\Models\AccountLog;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccountLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('occurred_at')
                    ->label('Date & Time')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),

                TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'login' ? 'success' : 'warning')
                    ->formatStateUsing(fn (string $state): string => str($state)->headline()->toString())
                    ->sortable(),

                TextColumn::make('account_name')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->badge()
                    ->searchable(),

                TextColumn::make('panel')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->options([
                        'login' => 'Login',
                        'logout' => 'Logout',
                    ]),

                SelectFilter::make('role')
                    ->options(fn (): array => AccountLog::query()
                        ->whereNotNull('role')
                        ->distinct()
                        ->orderBy('role')
                        ->pluck('role', 'role')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Account Log Details')
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('account_name')->label('Account')->disabled(),
                                TextInput::make('username')->disabled(),
                                TextInput::make('email')->disabled(),
                                TextInput::make('role')->disabled(),
                                TextInput::make('event')->disabled(),
                                TextInput::make('panel')->disabled(),
                                TextInput::make('ip_address')->label('IP Address')->disabled(),
                                TextInput::make('occurred_at')->label('Date & Time')->disabled(),
                                Textarea::make('user_agent')
                                    ->label('User Agent')
                                    ->rows(4)
                                    ->disabled()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
