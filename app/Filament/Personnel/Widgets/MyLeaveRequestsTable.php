<?php

namespace App\Filament\Personnel\Widgets;

use App\Filament\Support\PersonnelLeaveForm;
use App\Models\Leave;
use App\Services\LeaveCreditService;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class MyLeaveRequestsTable extends TableWidget
{
    protected static ?int $sort = 2;

    public int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $employeeId = Filament::auth()->user()?->employee?->id;

        return $table
            ->heading('My Leave Requests')
            ->query(
                fn (): Builder => Leave::query()
                    ->where('employee_id', $employeeId)
                    ->latest('created_at')
            )
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('leave_type')
                    ->label('Leave Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Vacation Leave' => 'info',
                        'Sick Leave' => 'success',
                        'Halfday Leave' => 'warning',
                        'Birthday Leave' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('leave_from')
                    ->label('From')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('leave_to')
                    ->label('To')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('deducted_points')
                    ->label('Deducted')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->schema(PersonnelLeaveForm::schema(includeHrFields: true)),

                    EditAction::make()
                        ->schema(PersonnelLeaveForm::schema())
                        ->visible(fn (Leave $record): bool => $record->status === 'Pending')
                        ->mutateDataUsing(function (array $data, Leave $record): array {
                            $data['employee_id'] = $record->employee_id;

                            try {
                                return [
                                    ...$data,
                                    ...LeaveCreditService::assertCanCreateLeave($data, $record->id),
                                ];
                            } catch (ValidationException $exception) {
                                Notification::make()
                                    ->danger()
                                    ->title('Unable to update leave request')
                                    ->body(collect($exception->errors())->flatten()->first())
                                    ->send();

                                throw $exception;
                            }
                        }),

                    DeleteAction::make()
                        ->visible(fn (Leave $record): bool => $record->status === 'Pending'),
                ]),
            ])
            ->emptyStateHeading('No Leave Requests Yet')
            ->emptyStateDescription('You have not filed any leave requests. Click "Send Leave Request" above to submit an application.')
            ->emptyStateIcon(Heroicon::DocumentText);
    }
}
