<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpiringContractsTable extends TableWidget
{
    protected static ?string $heading = 'Expiring Contracts';

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Employee::query()
                ->with(['designation', 'department', 'user'])
                ->whereHas('user', fn (Builder $query): Builder => $query->where('role', 'employee'))
                ->whereIn('employment_type', ['Job Order', 'Casual'])
                ->whereNotNull('employment_start_date')
                ->whereNotNull('employment_end_date')
                ->whereDate('employment_end_date', '>=', now()->toDateString())
                ->whereDate('employment_end_date', '<=', now()->addMonth()->toDateString())
                ->orderBy('employment_end_date'))
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('full_name')
                    ->label('Employee Fullname'),
                TextColumn::make('days_remaining')
                    ->label('Days Left')
                    ->state(fn (Employee $record): int => max(0, now()->startOfDay()->diffInDays($record->employment_end_date, false)))
                    ->badge(),
            ]);
    }
}
