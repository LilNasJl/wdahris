<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class NearRetirementEmployeesTable extends TableWidget
{
    protected static ?string $heading = 'Near Retirement';

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Employee::query()
                ->with(['designation', 'department', 'user'])
                ->whereHas('user', fn (Builder $query): Builder => $query->where('role', 'employee'))
                ->whereNotIn('employment_type', ['Retired', 'Resigned', 'Terminated', 'Force Resigned', 'Death of Employee'])
                ->whereNotNull('birthdate')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60')
                ->orderBy('birthdate'))
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('full_name')
                    ->label('Employee Fullname')
                    ->description(fn (Employee $record): ?string => $record->designation?->name ?? $record->department?->name)
                    ->weight('medium'),
                TextColumn::make('age')
                    ->label('Age')
                    ->state(fn (Employee $record): int => (int) $record->birthdate?->age)
                    ->badge()
                    ->color('warning'),
            ])
            ->emptyStateHeading('No employees near retirement')
            ->emptyStateDescription('All active personnel are currently below 60 years of age.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->paginated(false);
    }
}

