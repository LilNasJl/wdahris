<?php

namespace App\Filament\Pages;

use App\Filament\Support\ServiceCreditForm;
use App\Filament\Widgets\ServiceCreditsProfileCards;
use App\Filament\Widgets\ServiceCreditsProfileTable;
use App\Models\Employee;
use App\Models\ServiceCredits as ServiceCreditRecord;
use App\Models\ServiceCreditSignatory;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;

class ServiceCreditsProfilePage extends Page
{
    public Employee $employee;

    protected string $view = 'filament.pages.service-credits-profile-page';

    protected static ?string $title = 'Service Credits Overview';

    protected static ?string $slug = 'service-credits/{employee}/overview';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee->load(['designation', 'serviceCreditSignatory']);
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('return')
                ->label('Return')
                ->icon(Heroicon::ArrowLeft)
                ->url(ServiceCredits::getUrl()),

            ActionGroup::make([
                Action::make('exportPdf')
                    ->label('PDF')
                    ->icon(Heroicon::DocumentText)
                    ->url(fn (): string => route('service-credits.export', [
                        'employee' => $this->employee,
                        'format' => 'pdf',
                    ]))
                    ->openUrlInNewTab(),

                Action::make('print')
                    ->label('Print')
                    ->icon(Heroicon::Printer)
                    ->url(fn (): string => route('service-credits.export', [
                        'employee' => $this->employee,
                        'format' => 'print',
                    ]))
                    ->openUrlInNewTab(),
            ])
                ->label('Export')
                ->icon(Heroicon::ArrowDownTray)
                ->button(),

            ActionGroup::make([
                CreateAction::make('addServiceCredits')
                    ->label('Add Service Credits')
                    ->icon(Heroicon::Plus)
                    ->model(ServiceCreditRecord::class)
                    ->modalHeading('Add Service Credits')
                    ->schema(ServiceCreditForm::schema())
                    ->fillForm(fn (): array => $this->getCurrentAppointmentDefaults())
                    ->mutateDataUsing(fn (array $data): array => [
                        ...$data,
                        'employee_id' => $this->employee->id,
                    ])
                    ->createAnother(false)
                    ->successNotificationTitle('Service credit added')
                    ->successRedirectUrl(fn (): string => static::getUrl(['employee' => $this->employee->id])),

                Action::make('signatories')
                    ->label('Signatories')
                    ->icon(Heroicon::PencilSquare)
                    ->modalHeading('Service Credit Signatories')
                    ->modalSubmitActionLabel('Save')
                    ->schema(ServiceCreditForm::signatorySchema())
                    ->fillForm(fn (): array => $this->employee->serviceCreditSignatory?->only(['name', 'position']) ?? [])
                    ->action(function (array $data): void {
                        ServiceCreditSignatory::updateOrCreate(
                            ['employee_id' => $this->employee->id],
                            $data,
                        );
                    })
                    ->successNotificationTitle('Signatories saved')
                    ->successRedirectUrl(fn (): string => static::getUrl(['employee' => $this->employee->id])),
            ])
                ->label('Manage')
                ->icon(Heroicon::Cog6Tooth)
                ->button(),
        ];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            ServiceCreditsProfileCards::class,
            ServiceCreditsProfileTable::class,
        ];
    }

    #[Override]
    public function getWidgetData(): array
    {
        return [
            'employeeId' => $this->employee->id,
        ];
    }

    protected function getCurrentAppointmentDefaults(): array
    {
        $current = ServiceCreditRecord::query()
            ->where('employee_id', $this->employee->id)
            ->latest('to_date')
            ->latest('from_date')
            ->latest()
            ->first();

        return [
            'designation' => $current?->designation ?? $this->employee->designation?->title,
            'status' => $current?->status ?? $this->employee->employment_type,
            'salary' => $current?->salary ?? $this->employee->monthly_rate ?? ((float) $this->employee->daily_rate * 22),
            'station_place' => $current?->station_place,
            'branch' => $current?->branch,
        ];
    }
}
