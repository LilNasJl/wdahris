<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ServiceCreditsProfilePage;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ServiceCreditsEmployeeList extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(false)
            ->query(
                fn (): Builder => Employee::query()
                    ->whereHas('user', fn ($query) => $query->where('role', 'employee'))
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('uid')
                    ->label('ID No.')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => Employee::employeeIdFromUid($state) ?? 'N/A')
                    ->searchable(),
                TextColumn::make('fullname')
                    ->label('Employee Name')
                    ->getStateUsing(function ($record) {
                        return "{$record->lastname} {$record->middlename} {$record->firstname}";
                    })
                    ->searchable(['lastname', 'middlename', 'firstname']),
                TextColumn::make('designation.title')
                    ->label('Designation')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->limit(100)
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('manage')
                        ->label('Manage')
                        ->icon(Heroicon::Cog6Tooth)
                        ->url(fn (Employee $record): string => ServiceCreditsProfilePage::getUrl(['employee' => $record])),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
