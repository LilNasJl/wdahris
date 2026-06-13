<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Employee;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'employee'))
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('employee.uid')
                    ->label('ID No.')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => Employee::employeeIdFromUid($state) ?? 'N/A'),
                TextColumn::make('employee_full_name')
                    ->label('Employee Fullname')
                    ->state(fn (User $record): string => $record->employee?->full_name ?? $record->username ?? 'N/A')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search): void {
                            $query
                                ->where('username', 'like', "%{$search}%")
                                ->orWhereHas('employee', function (Builder $query) use ($search): void {
                                    $query
                                        ->where('firstname', 'like', "%{$search}%")
                                        ->orWhere('middlename', 'like', "%{$search}%")
                                        ->orWhere('lastname', 'like', "%{$search}%");
                                });
                        });
                    }),
                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
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
                    ViewAction::make(),
                    EditAction::make(),
                ])
                    ->icon(Heroicon::EllipsisHorizontal),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
