<?php

namespace App\Filament\Widgets;

use App\Models\Deduction;
use App\Models\Designation;
use App\Models\Employee as ModelsEmployee;
use App\Models\EmployeeDeduction;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class EmployeeDetailsTable extends TableWidget
{
    protected static ?string $heading = '';

    protected static bool $isDiscovered = false;

    protected function getDeductionFields(): array
    {
        return Deduction::query()
            ->orderBy('title')
            ->get()
            ->map(
                fn (Deduction $deduction) => TextInput::make("deductions.{$deduction->id}")
                    ->label($deduction->title)
                    ->numeric()
                    ->prefix('₱')
                    ->default(0)
                    ->minValue(0)
            )
            ->all();
    }

    protected function getDeductionState(ModelsEmployee $record): array
    {
        $saved = $record->employeeDeductions()
            ->pluck('amount', 'deduction_id')
            ->toArray();

        return Deduction::query()
            ->pluck('id')
            ->mapWithKeys(fn ($id) => [(string) $id => $saved[$id] ?? 0])
            ->toArray();
    }

    protected function getEmployeeFormSchema(): array
    {
        return [
            Tabs::make('Employee Details')
                ->tabs([
                    Tabs\Tab::make('Basic Info')
                        ->icon('heroicon-s-user')
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextInput::make('firstname')
                                        ->placeholder('e.g., Juan')
                                        ->required(),

                                    TextInput::make('middlename')
                                        ->placeholder('e.g., Dela | Leave it empty if not applicable'),

                                    TextInput::make('lastname')
                                        ->placeholder('e.g., Cruz')
                                        ->required(),
                                ])
                                ->columns(3),

                            Group::make()
                                ->schema([
                                    DatePicker::make('birthdate')
                                        ->placeholder('Birth Date')
                                        ->required(),

                                    Select::make('status')
                                        ->label('Civil Status')
                                        ->options([
                                            'single' => 'Single',
                                            'married' => 'Married',
                                            'divorce' => 'Divorce',
                                            'widowed' => 'Widowed',
                                            'separated' => 'Separated',
                                            'annuled' => 'Annuled',
                                        ])
                                        ->required(),

                                    Select::make('gender')
                                        ->label('Gender')
                                        ->options([
                                            'male' => 'male',
                                            'female' => 'female',
                                        ])
                                        ->required(),

                                    TextInput::make('email')
                                        ->label('Email Address')
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(
                                            table: User::class,
                                            column: 'email',
                                            ignorable: fn (?ModelsEmployee $record): ?User => $record?->user,
                                        ),

                                    TextInput::make('mobile')
                                        ->label('Mobile #')
                                        ->tel()
                                        ->mask('99999999999')
                                        ->placeholder('09123456789')
                                        ->regex('/^09\d{9}$/')
                                        ->validationMessages([
                                            'regex' => 'The mobile number must start with 09 and be exactly 11 digits.',
                                        ])
                                        ->required(),

                                    TextInput::make('kids')
                                        ->label('No. of Kids')
                                        ->placeholder('0 if not applicable')
                                        ->integer()
                                        ->maxValue(20)
                                        ->required(),

                                    TextInput::make('address')
                                        ->label('Permanent Address')
                                        ->placeholder('e.g., Tagum City, Philippines')
                                        ->required()
                                        ->columnSpanFull(),

                                ])
                                ->columns(3),

                            Placeholder::make('divider2')
                                ->label("GOVERNMENT ID's")
                                ->content(new HtmlString('<hr class="my-4">')),

                            Group::make()
                                ->schema([
                                    TextInput::make('gsis')
                                        ->label('GSIS BP #')
                                        ->mask('99999999999')
                                        ->placeholder('20012345678')
                                        ->maxLength(11),

                                    TextInput::make('philhealth')
                                        ->label('PhilHealth ID #')
                                        ->mask('99-999999999-9')
                                        ->placeholder('01-234567890-1')
                                        ->regex('/^\d{2}-\d{9}-\d{1}$/')
                                        ->validationMessages([
                                            'regex' => 'The PhilHealth number must follow the 00-000000000-0 format.',
                                        ]),

                                    TextInput::make('pagibig')
                                        ->label('Pag-IBIG MID #')
                                        ->mask('9999-9999-9999')
                                        ->placeholder('1234-5678-9012')
                                        ->regex('/^\d{4}-\d{4}-\d{4}$/')
                                        ->validationMessages([
                                            'regex' => 'The Pag-IBIG number must be exactly 12 digits (0000-0000-0000).',
                                        ]),

                                    TextInput::make('tin')
                                        ->label('TIN #')
                                        ->mask('999-999-999')
                                        ->placeholder('000-000-000')
                                        ->regex('/^\d{3}-\d{3}-\d{3}$/')
                                        ->validationMessages([
                                            'regex' => 'The TIN format must be 000-000-000.',
                                        ]),

                                    TextInput::make('sss')
                                        ->label('SSS #')
                                        ->mask('99-9999999-9')
                                        ->placeholder('33-1234567-8')
                                        ->regex('/^\d{2}-\d{7}-\d{1}$/')
                                        ->validationMessages([
                                            'regex' => 'The SSS number must follow the 00-0000000-0 format.',
                                        ]),
                                ])
                                ->columns(2),
                        ]),

                    Tabs\Tab::make('Designation')
                        ->icon('heroicon-s-briefcase')
                        ->schema([
                            Select::make('designation_id')
                                ->relationship('designation', 'title')
                                ->live()
                                ->afterStateUpdated(fn (Set $set, mixed $state) => $set('salary_grade', Designation::query()->whereKey($state)->value('salary_grade') ?? ''))
                                ->required(),

                            TextInput::make('salary_grade')
                                ->label('Salary Grade')
                                ->required()
                                ->maxLength(100),

                            Select::make('department_id')
                                ->relationship('department', 'name')
                                ->required(),

                            DatePicker::make('hired_date')
                                ->label('Hired Date')
                                ->required(),

                            Select::make('employment_type')
                                ->label('Employment Type')
                                ->options([
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
                                ])
                                ->required(),
                        ])
                        ->columns(2),

                    Tabs\Tab::make('Education')
                        ->icon(Heroicon::AcademicCap)
                        ->schema([
                            TextInput::make('school_name')
                                ->label('School Name')
                                ->placeholder('e.g., University of Mindanao'),

                            Group::make()
                                ->schema([
                                    Select::make('school_level')
                                        ->label('Highest Level Attended')
                                        ->options([
                                            'Elementary' => 'Elementary',
                                            'Secondary' => 'Secondary (High School)',
                                            'Vocational' => 'Vocational / Trade Course',
                                            'College' => 'College (Tertiary)',
                                            'Graduate Studies' => 'Graduate Studies (Master\'s/Doctorate)',
                                        ]),

                                    DatePicker::make('year_grad')
                                        ->label('Year Graduated'),
                                ])
                                ->columns(2),
                        ]),

                    Tabs\Tab::make('Deductions')
                        ->icon(Heroicon::MinusCircle)
                        ->schema([
                            Group::make()
                                ->schema(fn (): array => $this->getDeductionFields())
                                ->columns(2),
                        ]),

                    Tabs\Tab::make('Salary')
                        ->icon(Heroicon::Banknotes)
                        ->schema([
                            Group::make()
                                ->schema([
                                    Select::make('rate_type')
                                        ->label('Rate Type')
                                        ->options([
                                            'daily' => 'Daily',
                                            'monthly' => 'Monthly',
                                        ])
                                        ->required(),

                                    Select::make('payment_type')
                                        ->label('Payment Type')
                                        ->options([
                                            'cash' => 'Cash',
                                            'atm' => 'ATM',
                                        ])
                                        ->required(),

                                    TextInput::make('daily_rate')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->required(),
                                ])
                                ->columns(2),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => ModelsEmployee::query()
                    ->whereHas('user', fn ($query) => $query->where('role', 'employee'))
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('uid')
                    ->label('ID No.')
                    ->badge()
                    ->formatStateUsing(
                        fn (ModelsEmployee $record): string => ModelsEmployee::employeeIdFromUid($record->uid) ?? 'N/A'
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('firstname')
                    ->label('Full Name')
                    ->formatStateUsing(
                        fn (ModelsEmployee $record): string => collect([
                            $record->firstname,
                            $record->middlename,
                            $record->lastname,
                        ])->filter()->implode(' ')
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('designation.title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([

                ActionGroup::make([
                    ViewAction::make()
                        ->modalIcon(Heroicon::UserCircle)
                        ->modalHeading('View Employee Details')
                        ->schema($this->getEmployeeFormSchema())
                        ->mutateRecordDataUsing(function (array $data, ModelsEmployee $record): array {
                            $data['deductions'] = $this->getDeductionState($record);
                            $data['email'] = $record->email ?: $record->user?->email;
                            $data['salary_grade'] = $record->designation?->salary_grade;

                            return $data;
                        }),

                    EditAction::make()
                        ->modalIcon(Heroicon::UserCircle)
                        ->modalHeading('Edit Employee Details')
                        ->schema($this->getEmployeeFormSchema())
                        ->mutateRecordDataUsing(function (array $data, ModelsEmployee $record): array {
                            $data['deductions'] = $this->getDeductionState($record);
                            $data['email'] = $record->email ?: $record->user?->email;
                            $data['salary_grade'] = $record->designation?->salary_grade;

                            return $data;
                        })
                        ->using(function (ModelsEmployee $record, array $data): Model {
                            $deductions = $data['deductions'] ?? [];
                            $salaryGrade = $data['salary_grade'] ?? null;
                            $email = $data['email'] ?? null;

                            unset($data['deductions'], $data['salary_grade']);

                            DB::transaction(function () use ($record, $data, $deductions, $salaryGrade, $email): void {
                                $record->update($data);

                                if (filled($email)) {
                                    $record->user?->update(['email' => $email]);
                                }

                                if (filled($salaryGrade) && $record->designation_id) {
                                    Designation::query()
                                        ->whereKey($record->designation_id)
                                        ->update(['salary_grade' => $salaryGrade]);
                                }

                                foreach ($deductions as $deductionId => $amount) {
                                    EmployeeDeduction::updateOrCreate(
                                        [
                                            'employee_id' => $record->id,
                                            'deduction_id' => $deductionId,
                                        ],
                                        [
                                            'amount' => $amount ?: 0,
                                        ],
                                    );
                                }
                            });

                            return $record->refresh();
                        }),
                ])
                    ->icon(Heroicon::EllipsisHorizontal),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
