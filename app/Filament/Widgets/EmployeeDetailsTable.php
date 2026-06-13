<?php

namespace App\Filament\Widgets;

use App\Models\Deduction;
use App\Models\Designation;
use App\Models\Employee as ModelsEmployee;
use App\Models\EmployeeDeduction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
                    ->prefix('PHP')
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
                    Tabs\Tab::make('Personal Information')
                        ->icon('heroicon-s-user')
                        ->schema([
                            Section::make('Employee Information')
                                ->schema([
                                    TextInput::make('employee_id_number')
                                        ->label('Employee ID #')
                                        ->placeholder('A685-00001')
                                        ->required()
                                        ->regex('/^A685-\d{5}$/')
                                        ->validationMessages([
                                            'regex' => 'The employee ID must follow the A685-00001 format.',
                                        ]),

                                    $this->upperTextInput('lastname')
                                        ->label('Last Name')
                                        ->placeholder('CRUZ')
                                        ->required(),

                                    $this->upperTextInput('firstname')
                                        ->label('First Name')
                                        ->placeholder('JUAN')
                                        ->required(),

                                    $this->upperTextInput('middlename')
                                        ->label('Middle Name')
                                        ->placeholder('DELA'),

                                    $this->upperTextInput('name_extension')
                                        ->label('Name Extension')
                                        ->placeholder('JR., SR., III')
                                        ->maxLength(20),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),

                            Section::make('Personal Information')
                                ->schema([
                                    Select::make('gender')
                                        ->label('Sex')
                                        ->options([
                                            'MALE' => 'MALE',
                                            'FEMALE' => 'FEMALE',
                                        ])
                                        ->required(),

                                    DatePicker::make('birthdate')
                                        ->label('Date of Birth')
                                        ->required(),

                                    $this->upperTextInput('place_of_birth')
                                        ->label('Place of Birth')
                                        ->maxLength(255),

                                    Select::make('status')
                                        ->label('Civil Status')
                                        ->options([
                                            'SINGLE' => 'SINGLE',
                                            'MARRIED' => 'MARRIED',
                                            'WIDOWED' => 'WIDOWED',
                                            'LEGALLY SEPARATED' => 'LEGALLY SEPARATED',
                                            'ANNULED/DIVORCED' => 'ANNULED/DIVORCED',
                                            'COMMON-LAW/LIVE-IN' => 'COMMON-LAW/LIVE-IN',
                                        ])
                                        ->required(),

                                    $this->upperTextInput('citizenship')
                                        ->label('Citizenship')
                                        ->maxLength(100)
                                        ->required(),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),

                            Section::make('Health Information')
                                ->schema([
                                    Select::make('blood_type')
                                        ->label('Blood Type')
                                        ->options([
                                            'O Positive (O+)' => 'O Positive (O+)',
                                            'O Negative (O-)' => 'O Negative (O-)',
                                            'A Positive (A+)' => 'A Positive (A+)',
                                            'A Negative (A-)' => 'A Negative (A-)',
                                            'B Positive (B+)' => 'B Positive (B+)',
                                            'B Negative (B-)' => 'B Negative (B-)',
                                            'AB Positive (AB+)' => 'AB Positive (AB+)',
                                            'AB Negative (AB-)' => 'AB Negative (AB-)',
                                            'Bombay Phenotype' => 'Bombay Phenotype',
                                            'Rh-null' => 'Rh-null',
                                        ]),

                                    TextInput::make('height_cm')
                                        ->label('Height (cm)')
                                        ->numeric()
                                        ->minValue(0),

                                    TextInput::make('weight_kg')
                                        ->label('Weight (kg)')
                                        ->numeric()
                                        ->minValue(0),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 3,
                                ]),

                            Section::make('Contact Information')
                                ->schema([
                                    TextInput::make('mobile')
                                        ->label('Contact Number')
                                        ->tel()
                                        ->mask('99999999999')
                                        ->placeholder('09123456789')
                                        ->regex('/^09\d{9}$/')
                                        ->validationMessages([
                                            'regex' => 'The contact number must start with 09 and be exactly 11 digits.',
                                        ]),

                                    TextInput::make('email')
                                        ->label('Email Address')
                                        ->email()
                                        ->maxLength(255),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),

                            Section::make('Address Information')
                                ->schema([
                                    $this->upperTextarea('residential_address')
                                        ->label('Residential Address')
                                        ->columnSpanFull(),

                                    $this->upperTextarea('permanent_address')
                                        ->label('Permanent Address')
                                        ->columnSpanFull(),
                                ])
                                ->columns(1),
                        ]),

                    Tabs\Tab::make('Government')
                        ->icon(Heroicon::Identification)
                        ->schema([
                            Group::make()
                                ->schema([
                                    TextInput::make('gsis')
                                        ->label('GSIS Number')
                                        ->maxLength(20),

                                    TextInput::make('philhealth')
                                        ->label('PhilHealth Number')
                                        ->maxLength(20),

                                    TextInput::make('pagibig')
                                        ->label('Pag-IBIG Number')
                                        ->maxLength(20),

                                    TextInput::make('sss')
                                        ->label('SSS Number')
                                        ->maxLength(20),

                                    TextInput::make('tin')
                                        ->label('TIN Number')
                                        ->maxLength(20),

                                    TextInput::make('philsys')
                                        ->label('PhilSys Number (Optional)')
                                        ->maxLength(40),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),
                        ]),

                    Tabs\Tab::make('Employment')
                        ->icon('heroicon-s-briefcase')
                        ->schema([
                            Section::make('Position & Compensation')
                                ->schema([
                                    Select::make('designation_id')
                                        ->label('Position Title')
                                        ->relationship('designation', 'title')
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set, mixed $state) => $set('salary_grade', Designation::query()->whereKey($state)->value('salary_grade') ?? null))
                                        ->required(),

                                    TextInput::make('salary_grade')
                                        ->label('Salary Grade')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(40),

                                    TextInput::make('salary_step')
                                        ->label('Salary Step')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(10),

                                    $this->upperTextInput('plantilla_item_number')
                                        ->label('Plantilla Item Number'),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),

                            Section::make('Department & Reporting')
                                ->schema([
                                    Select::make('department_id')
                                        ->label('Department')
                                        ->relationship('department', 'name')
                                        ->required(),

                                    $this->upperTextInput('office_assignment')
                                        ->label('Office Assignment'),

                                    $this->upperTextInput('immediate_supervisor')
                                        ->label('Immediate Supervisor'),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),

                            Section::make('Dates & Timeline')
                                ->schema([
                                    DatePicker::make('hired_date')
                                        ->label('Date Hired'),

                                    DatePicker::make('original_appointment_date')
                                        ->label('Original Appointment Date'),

                                    DatePicker::make('assumption_date')
                                        ->label('Date of Assumption'),

                                    DatePicker::make('employment_start_date')
                                        ->label('Employment Start Date'),

                                    DatePicker::make('employment_end_date')
                                        ->label('Employment End Date'),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),

                            Section::make('Employment Status & Details')
                                ->schema([
                                    Select::make('employment_type')
                                        ->label('Employment Status')
                                        ->options([
                                            'Permanent' => 'Permanent',
                                            'Temporary' => 'Temporary',
                                            'Coterminous' => 'Coterminous',
                                            'Contractual' => 'Contractual',
                                            'Casual' => 'Casual',
                                            'Job Order' => 'Job Order',
                                            'Contract of Service' => 'Contract of Service',
                                            'Substitute' => 'Substitute',
                                            'Retired' => 'Retired',
                                            'Resigned' => 'Resigned',
                                            'Terminated' => 'Terminated',
                                            'Force Resigned' => 'Force Resigned',
                                            'Death of Employee' => 'Death of Employee',
                                        ]),

                                    $this->upperTextInput('employment_classification')
                                        ->label('Employment Classification'),

                                    $this->upperTextarea('employment_remarks')
                                        ->label('Employment Remarks')
                                        ->columnSpanFull(),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),
                        ]),

                    Tabs\Tab::make('Education')
                        ->icon(Heroicon::AcademicCap)
                        ->schema([
                            Repeater::make('educations')
                                ->label('Educational Backgrounds')
                                ->schema([
                                    $this->upperTextInput('school_name')
                                        ->label('School Name')
                                        ->maxLength(250)
                                        ->required(),

                                    Select::make('school_level')
                                        ->label('Highest Level Attended')
                                        ->options([
                                            'ELEMENTARY' => 'ELEMENTARY',
                                            'SECONDARY' => 'SECONDARY (HIGH SCHOOL)',
                                            'VOCATIONAL' => 'VOCATIONAL / TRADE COURSE',
                                            'COLLEGE' => 'COLLEGE (TERTIARY)',
                                            'GRADUATE STUDIES' => 'GRADUATE STUDIES (MASTER\'S/DOCTORATE)',
                                        ])
                                        ->required(),

                                    DatePicker::make('year_grad')
                                        ->label('Year Graduated'),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                ->addActionLabel('Add Education Entry')
                                ->reorderable(false),
                        ]),

                    Tabs\Tab::make('Deductions')
                        ->icon(Heroicon::MinusCircle)
                        ->schema([
                            Group::make()
                                ->schema(fn (): array => $this->getDeductionFields())
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),
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
                                        ]),

                                    Select::make('payment_type')
                                        ->label('Payment Type')
                                        ->options([
                                            'cash' => 'Cash',
                                            'atm' => 'ATM',
                                        ]),

                                    TextInput::make('daily_rate')
                                        ->label('Daily Rate')
                                        ->numeric()
                                        ->prefix('PHP')
                                        ->minValue(0),

                                    TextInput::make('monthly_rate')
                                        ->label('Monthly Rate')
                                        ->numeric()
                                        ->prefix('PHP')
                                        ->minValue(0),

                                    TextInput::make('allowance')
                                        ->numeric()
                                        ->prefix('PHP')
                                        ->minValue(0),
                                ])
                                ->columns([
                                    'default' => 1,
                                    'md' => 2,
                                ]),
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
                    ->with(['designation', 'department', 'user'])
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

                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search): void {
                            $query
                                ->where('firstname', 'like', "%{$search}%")
                                ->orWhere('middlename', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('lastname', $direction)),

                TextColumn::make('designation.title')
                    ->label('Position Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalIcon(Heroicon::UserCircle)
                        ->modalHeading('View Employee Details')
                        ->modalWidth(Width::SevenExtraLarge)
                        ->schema($this->getEmployeeFormSchema())
                        ->mutateRecordDataUsing(fn (array $data, ModelsEmployee $record): array => $this->prepareEmployeeRecordData($data, $record)),

                    EditAction::make()
                        ->modalIcon(Heroicon::UserCircle)
                        ->modalHeading('Edit Employee Details')
                        ->modalWidth(Width::SevenExtraLarge)
                        ->schema($this->getEmployeeFormSchema())
                        ->mutateRecordDataUsing(fn (array $data, ModelsEmployee $record): array => $this->prepareEmployeeRecordData($data, $record))
                        ->using(function (ModelsEmployee $record, array $data): Model {
                            $deductions = $data['deductions'] ?? [];
                            $educations = $data['educations'] ?? [];
                            $employeeIdNumber = $data['employee_id_number'] ?? ModelsEmployee::employeeIdFromUid($record->uid);

                            unset($data['deductions'], $data['educations'], $data['employee_id_number']);

                            $uid = ModelsEmployee::normalizeEmployeeUid($employeeIdNumber);

                            $duplicate = ModelsEmployee::withTrashed()
                                ->where('uid', $uid)
                                ->whereKeyNot($record->getKey())
                                ->exists();

                            if ($duplicate) {
                                throw ValidationException::withMessages([
                                    'employee_id_number' => 'This employee ID number is already assigned to another employee.',
                                ]);
                            }

                            DB::transaction(function () use ($record, $data, $deductions, $educations, $uid): void {
                                $data = $this->normalizeEmployeeData($data);
                                $data['uid'] = $uid;
                                $data['address'] = $data['permanent_address'] ?? $data['address'] ?? null;

                                $firstEducation = collect($educations)
                                    ->first(fn (array $education): bool => filled($education['school_name'] ?? null) || filled($education['school_level'] ?? null) || filled($education['year_grad'] ?? null));

                                $data['school_name'] = $firstEducation['school_name'] ?? null;
                                $data['school_level'] = $firstEducation['school_level'] ?? null;
                                $data['year_grad'] = $firstEducation['year_grad'] ?? null;

                                $record->update($data);

                                $employeeIdNumber = ModelsEmployee::employeeIdFromUid($uid);
                                $record->user?->update([
                                    'username' => $employeeIdNumber,
                                    'name' => $employeeIdNumber,
                                    'email' => filled($record->user?->email)
                                        ? $record->user->email
                                        : strtolower(str_replace('-', '', $employeeIdNumber)).'@wda.local',
                                ]);

                                $record->educations()->delete();

                                foreach ($educations as $education) {
                                    if (blank($education['school_name'] ?? null) && blank($education['school_level'] ?? null) && blank($education['year_grad'] ?? null)) {
                                        continue;
                                    }

                                    $record->educations()->create([
                                        'school_name' => mb_strtoupper(trim((string) ($education['school_name'] ?? ''))),
                                        'school_level' => $education['school_level'] ?? null,
                                        'year_grad' => $education['year_grad'] ?? null,
                                    ]);
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

    protected function prepareEmployeeRecordData(array $data, ModelsEmployee $record): array
    {
        $data['employee_id_number'] = ModelsEmployee::employeeIdFromUid($record->uid);
        $data['deductions'] = $this->getDeductionState($record);
        $data['email'] = $record->email ?: $record->user?->email;
        $data['permanent_address'] = $record->permanent_address ?: $record->address;
        $data['salary_grade'] = $record->salary_grade ?: $record->designation?->salary_grade;
        $data['educations'] = $record->educations()
            ->get(['school_name', 'school_level', 'year_grad'])
            ->map(fn ($education): array => [
                'school_name' => $education->school_name,
                'school_level' => $education->school_level,
                'year_grad' => $education->year_grad,
            ])
            ->whenEmpty(function ($collection) use ($record) {
                if (blank($record->school_name) && blank($record->school_level) && blank($record->year_grad)) {
                    return $collection;
                }

                return collect([[
                    'school_name' => $record->school_name,
                    'school_level' => $record->school_level,
                    'year_grad' => $record->year_grad,
                ]]);
            })
            ->toArray();

        foreach (['firstname', 'middlename', 'lastname', 'name_extension', 'gender', 'status', 'place_of_birth', 'citizenship', 'residential_address', 'permanent_address'] as $field) {
            if (filled($data[$field] ?? null)) {
                $data[$field] = mb_strtoupper((string) $data[$field]);
            }
        }

        return $data;
    }

    protected function normalizeEmployeeData(array $data): array
    {
        foreach ([
            'firstname',
            'middlename',
            'lastname',
            'name_extension',
            'gender',
            'status',
            'place_of_birth',
            'citizenship',
            'residential_address',
            'permanent_address',
            'immediate_supervisor',
            'employment_classification',
            'plantilla_item_number',
            'office_assignment',
            'employment_remarks',
        ] as $field) {
            if (array_key_exists($field, $data) && filled($data[$field])) {
                $data[$field] = mb_strtoupper(trim((string) $data[$field]));
            }
        }

        return $data;
    }

    protected function upperTextInput(string $name): TextInput
    {
        return TextInput::make($name)
            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
            ->dehydrateStateUsing(fn ($state): ?string => filled($state) ? mb_strtoupper(trim((string) $state)) : null);
    }

    protected function upperTextarea(string $name): Textarea
    {
        return Textarea::make($name)
            ->extraInputAttributes(['style' => 'text-transform: uppercase'])
            ->dehydrateStateUsing(fn ($state): ?string => filled($state) ? mb_strtoupper(trim((string) $state)) : null);
    }
}
