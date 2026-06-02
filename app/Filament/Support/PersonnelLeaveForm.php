<?php

namespace App\Filament\Support;

use App\Services\LeaveCreditService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class PersonnelLeaveForm
{
    public static function schema(bool $includeHrFields = false): array
    {
        $schema = [
            Section::make('Leave Request')
                ->schema([
                    Group::make()
                        ->schema([
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
                                ->rows(4)
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                ]),
        ];

        if ($includeHrFields) {
            $schema[] = Section::make('HR Action')
                ->schema([
                    Group::make()
                        ->schema([
                            TextInput::make('status')
                                ->disabled()
                                ->dehydrated(false),

                            Textarea::make('approval_comment')
                                ->label('HR Comment')
                                ->rows(3)
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('approved_checked_by_hr')
                                ->label('Approved and Checked by HR')
                                ->disabled()
                                ->dehydrated(false),
                        ])
                        ->columns(2),
                ]);
        }

        return $schema;
    }
}
