<?php

namespace App\Filament\Resources\Leaves\Schemas;

use App\Models\Employee;
use App\Services\LeaveCreditService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeaveForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make([
                            Select::make('employee_id')
                                ->label('Employee Name')
                                ->searchable()
                                ->getSearchResultsUsing(
                                    fn (string $search) => Employee::query()
                                        ->whereHas('user', fn ($query) => $query->where('role', 'employee'))
                                        ->where(function ($query) use ($search): void {
                                            $query
                                                ->where('firstname', 'like', "%{$search}%")
                                                ->orWhere('middlename', 'like', "%{$search}%")
                                                ->orWhere('lastname', 'like', "%{$search}%");
                                        })
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(fn (Employee $employee): array => [
                                            $employee->id => "{$employee->lastname}, {$employee->firstname} {$employee->middlename}",
                                        ])
                                )
                                ->getOptionLabelUsing(
                                    fn ($value) => optional(Employee::find($value))->full_name
                                )
                                ->required(),

                            Select::make('leave_type')
                                ->label('Leave Type')
                                ->options([
                                    LeaveCreditService::VACATION => LeaveCreditService::VACATION,
                                    LeaveCreditService::SICK => LeaveCreditService::SICK,
                                    LeaveCreditService::HALFDAY => LeaveCreditService::HALFDAY,
                                ])
                                ->searchable()
                                ->required(),

                            DatePicker::make('leave_from')
                                ->label('Leave From')
                                ->required(),

                            DatePicker::make('leave_to')
                                ->label('Leave To')
                                ->required(),

                            Textarea::make('reason')
                                ->label('Reason')
                                ->placeholder('Reason...')
                                ->rows(5)
                                ->required()
                                ->columnSpanFull(),
                        ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
