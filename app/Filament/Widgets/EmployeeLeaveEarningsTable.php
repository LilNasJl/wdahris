<?php

namespace App\Filament\Widgets;

use App\Filament\Support\LeaveCreditEarnedForm;
use App\Models\LeaveCreditEarned;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class EmployeeLeaveEarningsTable extends TableWidget
{
    public int|string|array $columnSpan = 'full';

    public ?int $employeeId = null;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Leaves Earned')
            ->query(
                fn (): Builder => LeaveCreditEarned::query()
                    ->where('employee_id', $this->employeeId)
                    ->latest('from_date')
                    ->latest()
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('from_date')
                    ->label('From')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('to_date')
                    ->label('To')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('particulars')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('vacation_earned')
                    ->label('VL Earned')
                    ->getStateUsing(fn (LeaveCreditEarned $record): string => in_array($record->leave_type, ['Vacation Leave', 'Vacation and Sick Leave'], true)
                        ? number_format((float) $record->earned, 2)
                        : ''),

                TextColumn::make('sick_earned')
                    ->label('SL Earned')
                    ->getStateUsing(fn (LeaveCreditEarned $record): string => in_array($record->leave_type, ['Sick Leave', 'Vacation and Sick Leave'], true)
                        ? number_format((float) $record->earned, 2)
                        : ''),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->schema(LeaveCreditEarnedForm::schema()),

                    EditAction::make()
                        ->schema(LeaveCreditEarnedForm::schema()),

                    DeleteAction::make(),
                ]),
            ])
            ->emptyStateHeading('No earned leave credits encoded');
    }
}
