<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\EmployeeLeaveCreditsPage;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LeaveCreditEmployeeList extends TableWidget
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

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['firstname', 'middlename', 'lastname'])
                    ->sortable(),

                TextColumn::make('designation.title')
                    ->label('Designation')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->wrap(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('viewLeaveCredits')
                        ->label('View Leave Credits')
                        ->icon(Heroicon::Eye)
                        ->url(fn (Employee $record): string => EmployeeLeaveCreditsPage::getUrl([
                            'employee' => $record,
                        ])),
                ]),
            ]);
    }
}
