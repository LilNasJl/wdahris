<?php

namespace App\Filament\Resources\SystemAccounts\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SystemAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query): Builder => $query
                    ->with('roles')
                    ->where('username', '!=', 'masteradmin')
                    ->where(function (Builder $query): void {
                        $query
                            ->where('role', '!=', 'employee')
                            ->orWhereHas('roles', fn (Builder $query): Builder => $query->where('name', '!=', 'employee'));
                    })
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => (string) str($state)->replace('_', ' ')->title()),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),

                    DeleteAction::make()
                        ->visible(fn (User $record): bool => Filament::auth()->id() !== $record->id),

                    RestoreAction::make(),

                    ForceDeleteAction::make()
                        ->visible(fn (User $record): bool => Filament::auth()->id() !== $record->id),
                ])
                    ->icon(Heroicon::EllipsisHorizontal),
            ])
            ->emptyStateHeading('No system accounts yet');
    }
}
