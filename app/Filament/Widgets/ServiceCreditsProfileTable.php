<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ServiceCreditsProfilePage;
use App\Filament\Support\ServiceCreditForm;
use App\Models\ServiceCredits as ModelsServiceCredits;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ServiceCreditsProfileTable extends TableWidget
{
    public int|string|array $columnSpan = 'full';

    public ?int $employeeId = null;

    public function table(Table $table): Table
    {
        return $table
            ->heading(false)
            ->query(
                fn (): Builder => ModelsServiceCredits::query()
                    ->where('employee_id', $this->employeeId)
                    ->latest('from_date')
                    ->latest('to_date')
                    ->latest()
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('from_date')
                    ->label('From')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('to_date')
                    ->label('To')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('designation')
                    ->label('Designation')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('status')
                    ->searchable()
                    ->badge(),
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
                        ->modalHeading('View Service Credit')
                        ->schema(ServiceCreditForm::schema()),

                    EditAction::make('update')
                        ->label('Update')
                        ->modalHeading('Update Service Credit')
                        ->schema(ServiceCreditForm::schema())
                        ->successNotificationTitle('Service credit updated')
                        ->successRedirectUrl(fn (): string => ServiceCreditsProfilePage::getUrl([
                            'employee' => $this->employeeId,
                        ])),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ])
            ->emptyStateHeading('No service credits encoded');
    }
}
