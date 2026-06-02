<?php

namespace App\Filament\Widgets;

use App\Models\LeaveCreditEarned;
use App\Models\LeaveCreditTransaction;
use App\Services\LeaveCreditService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class EmployeeLeaveCreditLedgerTable extends TableWidget
{
    public int|string|array $columnSpan = 'full';

    public ?int $employeeId = null;

    protected array $ledgerRows = [];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Leave Credits History')
            ->query(
                fn (): Builder => LeaveCreditTransaction::query()
                    ->where('employee_id', $this->employeeId)
                    ->whereIn('source_type', [LeaveCreditEarned::class, LeaveCreditService::MONETIZATION])
                    ->orderByRaw('COALESCE(from_date, created_at)')
                    ->orderBy('id')
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('from_date')
                    ->label('From')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->ledgerRow($record)['from_label'] ?? ''),

                TextColumn::make('to_date')
                    ->label('To')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->ledgerRow($record)['to_label'] ?? ''),

                TextColumn::make('particulars')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->ledgerRow($record)['particulars'] ?? '')
                    ->wrap(),

                TextColumn::make('vacation_earned')
                    ->label('VL Earned')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->formatNumeric($this->ledgerRow($record)['vacation_earned'] ?? null)),

                TextColumn::make('vacation_absence')
                    ->label('VL Abs./Und/WP')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->formatMixed($this->ledgerRow($record)['vacation_absence'] ?? null)),

                TextColumn::make('vacation_balance')
                    ->label('VL Bal')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->formatNumeric($this->ledgerRow($record)['vacation_balance'] ?? null)),

                TextColumn::make('sick_earned')
                    ->label('SL Earned')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->formatNumeric($this->ledgerRow($record)['sick_earned'] ?? null)),

                TextColumn::make('sick_absence')
                    ->label('SL Abs./Und/WP')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->formatMixed($this->ledgerRow($record)['sick_absence'] ?? null)),

                TextColumn::make('sick_balance')
                    ->label('SL Bal')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->formatNumeric($this->ledgerRow($record)['sick_balance'] ?? null)),

                TextColumn::make('action_taken')
                    ->label('Date & Action Taken')
                    ->getStateUsing(fn (LeaveCreditTransaction $record): string => $this->ledgerRow($record)['action_taken'] ?? '')
                    ->wrap(),
            ])
            ->emptyStateHeading('No leave credit history yet');
    }

    protected function ledgerRow(LeaveCreditTransaction $record): array
    {
        return collect($this->ledgerRows[(int) $record->employee_id] ??= LeaveCreditService::ledgerRows((int) $record->employee_id))
            ->first(fn (array $row): bool => ($row['source_type'] ?? null) === $record->source_type
                && (int) ($row['source_id'] ?? 0) === (int) ($record->source_type === LeaveCreditService::MONETIZATION ? $record->id : $record->source_id))
            ?? [];
    }

    protected function formatNumeric(mixed $value): string
    {
        return blank($value) ? '' : number_format((float) $value, 3);
    }

    protected function formatMixed(mixed $value): string
    {
        return is_numeric($value) ? number_format((float) $value, 3) : ($value ?: '');
    }
}
