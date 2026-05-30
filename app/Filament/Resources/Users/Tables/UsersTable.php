<?php

namespace App\Filament\Resources\Users\Tables;

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
                    ->formatStateUsing(fn ($state) => 'A685-'.$state),
                TextColumn::make('name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->searchable()
                    ->sortable(),
                // // Row index
                // TextColumn::make('index')
                //     ->label('#')
                //     ->rowIndex(),

                // // FULL NAME (fixed version)
                // TextColumn::make('firstname')
                //     ->label('Employee Name')
                //     ->formatStateUsing(function ($record) {
                //         return trim(
                //             $record->firstname . ' ' .
                //             ($record->middlename ? $record->middlename . ' ' : '') .
                //             $record->lastname
                //         );
                //     })
                //     ->searchable(query: function (Builder $query, string $search) {
                //         $query->where(function ($q) use ($search) {
                //             $q->where('firstname', 'like', "%{$search}%")
                //               ->orWhere('middlename', 'like', "%{$search}%")
                //               ->orWhere('lastname', 'like', "%{$search}%");
                //         });
                //     })
                //     ->sortable(query: function (Builder $query, string $direction) {
                //         $query->orderBy('firstname', $direction);
                //     }),

                // // Designation
                // TextColumn::make('designation.title')
                //     ->label('Designation')
                //     ->sortable()
                //     ->searchable(),

                // // Department
                // TextColumn::make('department.name')
                //     ->label('Department')
                //     ->sortable()
                //     ->searchable(),
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
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ]);
    }
}
