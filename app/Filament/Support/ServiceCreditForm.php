<?php

namespace App\Filament\Support;

use App\Models\Designation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class ServiceCreditForm
{
    public static function schema(): array
    {
        return [
            Section::make('SERVICE')
                ->schema([
                    DatePicker::make('from_date')
                        ->label('From')
                        ->required(),

                    DatePicker::make('to_date')
                        ->label('To')
                        ->required(),
                ])
                ->columns(2),

            Section::make('RECORD OF APPOINTMENT')
                ->schema([
                    Select::make('designation')
                        ->label('Position Title')
                        ->options(fn (): array => Designation::query()
                            ->orderBy('title')
                            ->pluck('title', 'title')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),

                    TextInput::make('salary_step')
                        ->label('Salary Step')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(40),

                    Select::make('status')
                        ->label('Employment Status')
                        ->options(self::statusOptions())
                        ->searchable()
                        ->required(),

                    TextInput::make('salary')
                        ->label('Salary Daily Rate')
                        ->numeric()
                        ->prefix('PHP')
                        ->minValue(0),

                    TextInput::make('station_place')
                        ->label('Station/Place')
                        ->maxLength(255),

                    TextInput::make('branch')
                        ->maxLength(255),
                ])
                ->columns(2),

            Section::make('LV. / AB. w/o Pay')
                ->schema([
                    TextInput::make('leave_without_pay')
                        ->label('Lv./AB w/o Pay')
                        ->maxLength(255),
                ]),

            Section::make('SEPARATION')
                ->schema([
                    DatePicker::make('separation_date')
                        ->label('Date'),

                    TextInput::make('separation_case')
                        ->label('Case')
                        ->maxLength(255),
                ])
                ->columns(2),

            Section::make('REMARKS')
                ->schema([
                    Textarea::make('remarks')
                        ->hiddenLabel()
                        ->rows(3),
                ]),
        ];
    }

    public static function signatorySchema(): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('position')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),
        ];
    }

    public static function statusOptions(): array
    {
        return [
            'Permanent' => 'Permanent (Plantilla)',
            'Temporary' => 'Temporary',
            'Coterminous' => 'Coterminous',
            'Contractual' => 'Contractual',
            'Casual' => 'Casual',
            'Job Order' => 'Job Order',
            'Contract of Service' => 'Contract of Service',
            'Substitute' => 'Substitute',
            'Resigned' => 'Resigned',
            'Terminated' => 'Terminated',
            'Force Resigned' => 'Force Resigned',
            'Death of Employee' => 'Death of Employee',
        ];
    }
}
