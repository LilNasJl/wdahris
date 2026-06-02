<?php

namespace App\Filament\Support;

use App\Services\LeaveCreditService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class LeaveCreditEarnedForm
{
    public static function schema(): array
    {
        return [
            Section::make('LEAVES EARNED')
                ->schema([
                    Group::make()
                        ->schema([
                            DatePicker::make('from_date')
                                ->label('From')
                                ->required(),

                            DatePicker::make('to_date')
                                ->label('To')
                                ->required(),

                            TextInput::make('particulars')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('earned')
                                ->label('Earned')
                                ->numeric()
                                ->default(number_format(LeaveCreditService::DEFAULT_EARNED_POINTS, 3, '.', ''))
                                ->disabled()
                                ->dehydrated(false),
                        ])
                        ->columns(2),
                ]),
        ];
    }
}
