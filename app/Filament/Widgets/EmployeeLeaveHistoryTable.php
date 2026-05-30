<?php

namespace App\Filament\Widgets;

use App\Models\Leave;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class EmployeeLeaveHistoryTable extends TableWidget
{
    public int|string|array $columnSpan = 'full';

    public ?int $employeeId = null;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Leave History')
            ->query(
                fn (): Builder => Leave::query()
                    ->where('employee_id', $this->employeeId)
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('leave_from')
                    ->label('From')
                    ->date('M d, Y'),

                TextColumn::make('leave_to')
                    ->label('To')
                    ->date('M d, Y'),

                TextColumn::make('leave_type')
                    ->badge(),

                TextColumn::make('deducted_points')
                    ->label('Deducted')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('is_lwop')
                    ->label('LWOP')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('approval_comment')
                    ->label('Comment')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('No leave history yet');
    }
}
