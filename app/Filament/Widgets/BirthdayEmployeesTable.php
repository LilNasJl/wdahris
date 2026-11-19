<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class BirthdayEmployeesTable extends TableWidget
{
    protected static ?string $heading = 'Birthdays This Month';

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Employee::query()
                ->with(['designation', 'department', 'user'])
                ->whereHas('user', fn (Builder $query): Builder => $query->where('role', 'employee'))
                ->whereNotNull('birthdate')
                ->whereMonth('birthdate', now()->month)
                ->orderByRaw('DAY(birthdate)'))
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('full_name')
                    ->label('Employee Fullname')
                    ->description(fn (Employee $record): ?string => $record->designation?->name ?? $record->department?->name)
                    ->weight('medium'),
                TextColumn::make('birthdate')
                    ->label('Birthdate')
                    ->date('F d')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-cake')
                    ->sortable(),
            ])
            ->emptyStateHeading('No birthdays this month')
            ->emptyStateDescription('No employee birthdays recorded for ' . now()->format('F') . '.')
            ->emptyStateIcon('heroicon-o-gift')
            ->paginated([5, 10, 25]);
    }
}

