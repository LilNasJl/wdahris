<?php

namespace App\Filament\Resources\ActionLogs\Tables;

use App\Models\ActionLog;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActionLogsTable
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

                TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created', 'restored' => 'success',
                        'updated' => 'info',
                        'deleted', 'force_deleted', 'database_cleared' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->headline()->toString())
                    ->sortable(),

                TextColumn::make('actor_name')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model_label')
                    ->label('Module')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('record_label')
                    ->label('Record')
                    ->searchable()
                    ->wrap()
                    ->limit(40),

                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->limit(55),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options(fn (): array => ActionLog::query()
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->map(fn (string $action): string => str($action)->replace('_', ' ')->headline()->toString())
                        ->all()),

                SelectFilter::make('model_label')
                    ->label('Module')
                    ->options(fn (): array => ActionLog::query()
                        ->whereNotNull('model_label')
                        ->distinct()
                        ->orderBy('model_label')
                        ->pluck('model_label', 'model_label')
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Action Log Details')
                    ->schema([
                        Section::make('Action')
                            ->schema([
                                TextInput::make('occurred_at')->label('Date & Time')->disabled(),
                                TextInput::make('action')->disabled(),
                                TextInput::make('actor_name')->label('Account')->disabled(),
                                TextInput::make('actor_username')->label('Username')->disabled(),
                                TextInput::make('model_label')->label('Module')->disabled(),
                                TextInput::make('record_label')->label('Record')->disabled(),
                                Textarea::make('description')
                                    ->rows(3)
                                    ->disabled()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Changed Data')
                            ->schema([
                                Textarea::make('old_values')
                                    ->label('Old Values')
                                    ->formatStateUsing(fn ($state): string => self::formatJson($state))
                                    ->rows(8)
                                    ->disabled(),

                                Textarea::make('new_values')
                                    ->label('New Values')
                                    ->formatStateUsing(fn ($state): string => self::formatJson($state))
                                    ->rows(8)
                                    ->disabled(),
                            ])
                            ->columns(2),

                        Section::make('Request')
                            ->schema([
                                TextInput::make('url')->disabled()->columnSpanFull(),
                                TextInput::make('ip_address')->label('IP Address')->disabled(),
                                Textarea::make('user_agent')
                                    ->label('User Agent')
                                    ->rows(3)
                                    ->disabled()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    protected static function formatJson(mixed $state): string
    {
        if (blank($state)) {
            return '';
        }

        if (is_string($state)) {
            return $state;
        }

        return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '';
    }
}
