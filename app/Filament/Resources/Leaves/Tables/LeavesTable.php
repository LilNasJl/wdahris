<?php

namespace App\Filament\Resources\Leaves\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeavesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                TextColumn::make('employee.lastname')
                    ->label('Employee Name')
                    ->formatStateUsing(fn ($record) => $record->employee
                            ? "{$record->employee->lastname}, {$record->employee->firstname} {$record->employee->middlename}"
                            : 'N/A'
                    )
                    ->searchable(['firstname', 'lastname', 'middlename'])
                    ->sortable(),

                TextColumn::make('leave_type')
                    ->label('Leave Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('deducted_points')
                    ->label('Deducted')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('created_at')
                    ->label('Requested At')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status_updated_at')
                    ->label('Approved/Rejected Date'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Pending' => 'warning',   // yellow
                        'Approved' => 'success',  // green
                        'Rejected' => 'danger',   // red
                        default => 'gray',
                    }),

            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ActionGroup::make([
                        Action::make('approve')
                            ->label('Approve')
                            ->icon(Heroicon::HandThumbUp)
                            ->color('success')
                            ->modalHeading('Approve Leave Request')
                            ->modalSubmitActionLabel('Approve')
                            ->schema(self::approvalSchema())
                            ->visible(fn ($record): bool => $record->status === 'Pending')
                            ->action(fn ($record, array $data) => $record->update([
                                'status' => 'Approved',
                                'status_updated_at' => now(),
                                'approval_comment' => $data['approval_comment'],
                                'approved_checked_by_hr' => $data['approved_checked_by_hr'],
                            ])),

                        Action::make('reject')
                            ->label('Reject')
                            ->icon(Heroicon::HandThumbDown)
                            ->color('danger')
                            ->modalHeading('Reject Leave Request')
                            ->modalSubmitActionLabel('Reject')
                            ->schema(self::approvalSchema())
                            ->visible(fn ($record): bool => $record->status === 'Pending')
                            ->action(fn ($record, array $data) => $record->update([
                                'status' => 'Rejected',
                                'status_updated_at' => now(),
                                'approval_comment' => $data['approval_comment'],
                                'approved_checked_by_hr' => $data['approved_checked_by_hr'],
                            ])),
                    ])->label('Approval Action'),

                    ViewAction::make(),
                    // EditAction::make(),
                ]),
                // ->icon(Heroicon::EllipsisHorizontal)
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //     ForceDeleteBulkAction::make(),
                //     RestoreBulkAction::make(),
                // ]),
            ]);
    }

    protected static function approvalSchema(): array
    {
        return [
            Textarea::make('approval_comment')
                ->label('Comment')
                ->required()
                ->rows(4),

            TextInput::make('approved_checked_by_hr')
                ->label('Approved and Checked by HR')
                ->required()
                ->maxLength(255),
        ];
    }
}
