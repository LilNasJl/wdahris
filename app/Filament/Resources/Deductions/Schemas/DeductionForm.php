<?php

namespace App\Filament\Resources\Deductions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DeductionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('title')
                                    ->placeholder('Deduction title')
                                    ->prefixIcon(Heroicon::Pencil)
                                    ->maxLength(100)

                                    ->required(),
                                Textarea::make('description')
                                    ->placeholder('Description...')
                                    ->maxLength(255)
                                    ->rows(5)
                                    ->columnSpanFull()
                                    ->required()
                            ])->columns(2)
                    ])->columnSpanFull()
            ]);
    }
}
