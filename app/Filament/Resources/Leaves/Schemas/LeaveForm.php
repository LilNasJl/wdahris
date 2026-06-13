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
                                ->options(fn (): array => self::employeeOptions())
                                ->searchable()
                                ->preload()
                                ->getSearchResultsUsing(
                                    fn (string $search): array => self::employeeOptions($search, 50)
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
                                    LeaveCreditService::BIRTHDAY => LeaveCreditService::BIRTHDAY,
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

    private static function employeeOptions(?string $search = null, ?int $limit = null): array
    {
        return Employee::query()
            ->whereHas('user', fn ($query) => $query->where('role', 'employee'))
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('firstname', 'like', "%{$search}%")
                        ->orWhere('middlename', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('uid', 'like', "%{$search}%");
                });
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->when($limit, fn ($query, int $limit) => $query->limit($limit))
            ->get()
            ->mapWithKeys(fn (Employee $employee): array => [
                $employee->id => $employee->full_name,
            ])
            ->all();
    }
}
