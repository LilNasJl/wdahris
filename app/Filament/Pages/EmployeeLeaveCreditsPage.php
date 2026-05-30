<?php

namespace App\Filament\Pages;

use App\Filament\Support\LeaveCreditEarnedForm;
use App\Filament\Widgets\EmployeeLeaveCreditCards;
use App\Filament\Widgets\EmployeeLeaveEarningsTable;
use App\Models\Employee;
use App\Models\LeaveCreditEarned;
use App\Services\LeaveCreditService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\ValidationException;
use Override;

class EmployeeLeaveCreditsPage extends Page
{
    public Employee $employee;

    protected string $view = 'filament.pages.employee-leave-credits-page';

    protected static ?string $title = 'Leave Credits';

    protected static ?string $slug = 'leave-credits/{employee}/overview';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee->load(['designation', 'department']);
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('return')
                ->label('Return')
                ->icon(Heroicon::ArrowLeft)
                ->url(ManageLeaveCredits::getUrl()),

            ActionGroup::make([
                CreateAction::make('addLeavesEarned')
                    ->label('Add Leaves Earn')
                    ->icon(Heroicon::Plus)
                    ->model(LeaveCreditEarned::class)
                    ->modalHeading('Add Leaves Earn')
                    ->schema(LeaveCreditEarnedForm::schema())
                    ->mutateDataUsing(fn (array $data): array => [
                        ...$data,
                        'employee_id' => $this->employee->id,
                        'leave_type' => LeaveCreditService::BOTH,
                        'earned' => LeaveCreditService::DEFAULT_EARNED_POINTS,
                    ])
                    ->createAnother(false)
                    ->successNotificationTitle('Leaves earned added')
                    ->successRedirectUrl(fn (): string => static::getUrl(['employee' => $this->employee])),

                Action::make('leaveHistory')
                    ->label('Leave History')
                    ->icon(Heroicon::CalendarDays)
                    ->url(fn (): string => EmployeeLeaveHistoryPage::getUrl(['employee' => $this->employee])),

                Action::make('leaveCreditHistory')
                    ->label('Leave Credit History')
                    ->icon(Heroicon::ClipboardDocumentList)
                    ->url(fn (): string => EmployeeLeaveCreditHistoryPage::getUrl(['employee' => $this->employee])),
            ])
                ->label('Manage')
                ->icon(Heroicon::Cog6Tooth)
                ->button(),

            Action::make('monetization')
                ->label('Monetization')
                ->icon(Heroicon::Banknotes)
                ->modalHeading('Monetization')
                ->modalSubmitActionLabel('Convert')
                ->schema([
                    Placeholder::make('current_points')
                        ->label('Current Points')
                        ->content(fn (): string => number_format(LeaveCreditService::availableCredits($this->employee->id)['total'], 2)),

                    Placeholder::make('current_vacation_points')
                        ->label('Current Vacation Leave Points')
                        ->content(fn (): string => number_format(LeaveCreditService::availableCredits($this->employee->id)['vacation'], 2)),

                    TextInput::make('points')
                        ->label('Points to Convert')
                        ->default(LeaveCreditService::MONETIZATION_POINTS)
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->failureNotificationTitle('Unable to monetize leave credits')
                ->action(function (Action $action): void {
                    try {
                        LeaveCreditService::monetizeCredits($this->employee->id);
                    } catch (ValidationException $exception) {
                        $action
                            ->failureNotificationBody(collect($exception->errors())->flatten()->first())
                            ->sendFailureNotification()
                            ->halt();
                    }
                })
                ->successNotificationTitle('Leave credits monetized')
                ->successRedirectUrl(fn (): string => static::getUrl(['employee' => $this->employee])),
        ];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            EmployeeLeaveCreditCards::class,
            EmployeeLeaveEarningsTable::class,
        ];
    }

    #[Override]
    public function getWidgetData(): array
    {
        return [
            'employeeId' => $this->employee->id,
        ];
    }
}
