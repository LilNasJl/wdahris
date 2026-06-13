<?php

namespace App\Filament\Resources\Designations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DesignationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Designation Details')
                    ->description('Add new designation')
                    ->schema([
                        TextInput::make('title')
                            ->label('Designation Title')
                            ->placeholder('e.g., Programmer 1')
                            ->maxLength(100)
                            ->required(),
                        TextInput::make('salary_grade')
                            ->label('Salary Grade (SG)')
                            ->placeholder('e.g., SG1')
                            ->maxLength(100)
                            ->required(),
                    ])->columns(2),
            ])->columns(1);
    }
}
