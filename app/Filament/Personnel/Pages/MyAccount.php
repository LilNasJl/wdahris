<?php

namespace App\Filament\Personnel\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MyAccount extends Page
{
    protected string $view = 'filament.personnel.pages.my-account';

    protected static ?string $title = 'My Account';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog6Tooth;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Filament::auth()->user();

        $this->form->fill([
            'username' => $user?->username,
            'email' => $user?->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        TextInput::make('username')
                            ->label('Username or Company ID')
                            ->placeholder('e.g., A685-00048')
                            ->prefixIcon('heroicon-m-user')
                            ->required()
                            ->unique(
                                table: User::class,
                                column: 'username',
                                ignorable: fn () => Filament::auth()->user(),
                            )
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->placeholder('e.g., employee@wda.local')
                            ->prefixIcon('heroicon-m-envelope')
                            ->email()
                            ->required()
                            ->unique(
                                table: User::class,
                                column: 'email',
                                ignorable: fn () => Filament::auth()->user(),
                            )
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('New Password')
                            ->placeholder('Leave empty to keep current password')
                            ->prefixIcon('heroicon-m-lock-closed')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->minLength(8)
                            ->dehydrated(fn ($state): bool => filled($state)),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->placeholder('Repeat your new password')
                            ->prefixIcon('heroicon-m-shield-check')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return;
        }

        $data = $this->form->getState();

        $updates = [
            'name' => $data['username'],
            'username' => $data['username'],
            'email' => $data['email'],
        ];

        if (filled($data['password'] ?? null)) {
            $updates['password'] = $data['password'];
        }

        $user->update($updates);

        $this->form->fill([
            'username' => $user->username,
            'email' => $user->email,
        ]);

        Notification::make()
            ->success()
            ->title('Account updated')
            ->send();
    }

    public function getEmployeeName(): string
    {
        return Filament::auth()->user()?->employee?->full_name
            ?? Filament::auth()->user()?->name
            ?? 'My Account';
    }
}
