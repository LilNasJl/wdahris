<?php

namespace App\Filament\Auth;

use App\Models\AccountLog;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\IconPosition;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Throwable;

class Login extends BaseLogin
{
    protected string $view = 'filament.auth.login';

    protected static string $layout = 'filament.auth.layout';

    public function getTitle(): string|Htmlable
    {
        return 'Water District Asuncion - HRIS';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username or ID #')
            ->placeholder('Username or 00001')
            ->prefixIcon('heroicon-m-user')
            ->prefixIconColor('primary')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->rule('regex:/^\S+$/')
            ->validationMessages([
                'regex' => 'The username must not contain spaces.',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::auth/pages/login.form.password.label'))
            ->placeholder('Enter your password')
            ->prefixIcon('heroicon-m-lock-closed')
            ->prefixIconColor('primary')
            ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="-1"> {{ __(\'filament-panels::auth/pages/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required();
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Sign in')
            ->icon('heroicon-m-arrow-right')
            ->iconPosition(IconPosition::After)
            ->submit('authenticate');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $password = $this->data['password'] ?? null;

        try {
            $response = parent::authenticate();
        } catch (\RuntimeException $exception) {
            if (str_contains($exception->getMessage(), 'Bcrypt algorithm')) {
                $this->throwFailureValidationException();
            }

            throw $exception;
        }

        if (! $response) {
            return null;
        }

        /** @var SessionGuard $guard */
        $guard = Filament::auth();
        $user = $guard->user();

        if (! $user) {
            return $response;
        }

        AccountLog::record($user, 'login');

        if (filled($password) && method_exists($guard, 'logoutOtherDevices')) {
            try {
                $guard->logoutOtherDevices($password);
            } catch (Throwable) {
                // The database session cleanup below still enforces one active session.
            }
        }

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->where('id', '!=', session()->getId())
                ->delete();
        }

        return $response;
    }
}
