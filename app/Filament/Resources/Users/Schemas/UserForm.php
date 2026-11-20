<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Employee;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employee Initial Details')
                    ->relationship('employee')
                    ->description('Personal identification information.')
                    ->schema([
                        TextInput::make('uid')
                            ->label('Employee ID #')
                            ->prefix('A685-')
                            ->placeholder('00001')
                            ->mask('99999')
                            ->formatStateUsing(fn ($state): string => filled($state)
                                ? str_pad((string) Employee::normalizeEmployeeUid($state), 5, '0', STR_PAD_LEFT)
                                : '')
                            ->required()
                            ->minLength(5)
                            ->maxLength(5)
                            ->live()
                            ->afterStateUpdated(function (Set $set, mixed $state): void {
                                $username = Employee::employeeIdFromUid($state) ?? '';

                                $set('../../username', $username);
                                $set('../username', $username);
                                $set('username', $username);
                            })
                            ->validationAttribute('employee ID')
                            ->rules(fn (?Model $record): array => [
                                function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                                    $uid = Employee::normalizeEmployeeUid($value);

                                    if ($uid < 1) {
                                        $fail('The employee ID number must be greater than zero.');

                                        return;
                                    }

                                    $employee = $record instanceof Employee
                                        ? $record
                                        : ($record instanceof User ? $record->employee : null);

                                    $exists = Employee::withTrashed()
                                        ->where('uid', $uid)
                                        ->when($employee?->getKey(), fn ($query, $key) => $query->whereKeyNot($key))
                                        ->exists();

                                    if ($exists) {
                                        $fail('This employee ID is already assigned to another employee.');
                                    }
                                },
                            ])
                            ->columnSpanFull(),

                        self::upperTextInput('firstname')
                            ->placeholder('JUAN')
                            ->required(),
                        self::upperTextInput('middlename')
                            ->placeholder('DELA'),
                        self::upperTextInput('lastname')
                            ->placeholder('CRUZ')
                            ->required(),
                        self::upperTextInput('name_extension')
                            ->label('Name Extension')
                            ->placeholder('JR., SR., III')
                            ->maxLength(20),
                        Select::make('gender')
                            ->label('Sex')
                            ->options([
                                'MALE' => 'MALE',
                                'FEMALE' => 'FEMALE',
                            ]),
                        DatePicker::make('birthdate')
                            ->required(),
                        DatePicker::make('hired_date')
                            ->label('Date Hired')
                            ->required(),
                        Select::make('designation_id')
                            ->label('Position Title')
                            ->relationship('designation', 'title')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Employee Account Details')
                    ->description('Primary login and security information.')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->placeholder('A685-00001')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                table: User::class,
                                column: 'username',
                                ignoreRecord: true,
                            )
                            ->validationAttribute('username')
                            ->validationMessages([
                                'unique' => 'This username is already registered.',
                            ]),

                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->placeholder('e.g., password123')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),
            ]);
    }

    protected static function upperTextInput(string $name): TextInput
    {
        return TextInput::make($name)
            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
            ->dehydrateStateUsing(fn ($state): ?string => filled($state) ? mb_strtoupper(trim((string) $state)) : null);
    }
}
