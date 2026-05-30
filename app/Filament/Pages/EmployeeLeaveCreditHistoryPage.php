<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\EmployeeLeaveCreditLedgerTable;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Override;

class EmployeeLeaveCreditHistoryPage extends Page
{
    public Employee $employee;

    protected string $view = 'filament.pages.employee-leave-credit-history-page';

    protected static ?string $title = 'Leave Credits History';

    protected static ?string $slug = 'leave-credits/{employee}/credit-history';

    protected static bool $shouldRegisterNavigation = false;

    public function mount(Employee $employee): void
    {
        $this->employee = $employee;
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('return')
                ->label('Return')
                ->icon(Heroicon::ArrowLeft)
                ->url(fn (): string => EmployeeLeaveCreditsPage::getUrl(['employee' => $this->employee])),

            ActionGroup::make([
                Action::make('pdf')
                    ->label('PDF')
                    ->icon(Heroicon::DocumentText)
                    ->url(fn (): string => route('leave-credits.export', [
                        'employee' => $this->employee,
                        'report' => 'credit-history',
                        'format' => 'pdf',
                    ]))
                    ->openUrlInNewTab(),

                Action::make('excel')
                    ->label('Excel')
                    ->icon(Heroicon::TableCells)
                    ->url(fn (): string => route('leave-credits.export', [
                        'employee' => $this->employee,
                        'report' => 'credit-history',
                        'format' => 'excel',
                    ])),

                Action::make('print')
                    ->label('Print')
                    ->icon(Heroicon::Printer)
                    ->url(fn (): string => route('leave-credits.export', [
                        'employee' => $this->employee,
                        'report' => 'credit-history',
                        'format' => 'print',
                    ]))
                    ->openUrlInNewTab(),
            ])
                ->label('Export')
                ->icon(Heroicon::ArrowDownTray)
                ->button(),
        ];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            EmployeeLeaveCreditLedgerTable::class,
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
