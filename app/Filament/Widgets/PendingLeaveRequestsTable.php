<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Leaves\LeaveResource;
use App\Models\Leave;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingLeaveRequestsTable extends TableWidget
{
    public int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pending Leave Requests')
            ->query(
                fn (): Builder => Leave::query()
                    ->with('employee')
                    ->where('status', 'Pending')
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('employee.firstname')
                    ->label('Employee')
                    ->formatStateUsing(fn (Leave $record): string => $record->employee?->full_name ?? 'N/A'),

                TextColumn::make('leave_type')
                    ->label('Leave Type')
                    ->badge(),

                TextColumn::make('leave_from')
                    ->label('From')
                    ->date('M d, Y'),

                TextColumn::make('leave_to')
                    ->label('To')
                    ->date('M d, Y'),

                TextColumn::make('deducted_points')
                    ->label('Deducted')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('created_at')
                    ->label('Requested')
                    ->since(),
            ])
            ->recordUrl(fn (): string => LeaveResource::getUrl())
            ->emptyStateHeading('No pending leave requests');
    }
}
