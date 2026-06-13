<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Department Details')
                    ->description('Create new department')
                    ->schema([
                        TextInput::make('name')
                            ->label('Department Name')
                            ->placeholder('e.g., Provincial Information Communication Technology Office')
                            ->maxLength(100)
                            ->required(),
                        TextInput::make('acronym')
                            ->label('Acronym')
                            ->placeholder('e.g., PICTO')
                            ->required(),
                    ])->columns(2),
            ])->columns(1);
    }
}
