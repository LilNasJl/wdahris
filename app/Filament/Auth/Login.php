<?php

namespace App\Filament\Auth;

use App\Models\AccountLog;
use App\Models\Employee;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Filament\Schemas\Components\Component;
use Filament\Support\Enums\IconPosition;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Timebox;
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
        $input = trim((string) ($data['username'] ?? ''));

        return [
            'username' => $this->resolveLoginUsername($input),
            'password' => $data['password'] ?? '',
        ];
    }

    protected function resolveLoginUsername(string $input): string
    {
        if (blank($input)) {
            return $input;
        }

        // 1. Direct match on username
        $user = User::where('username', $input)->first();
        if ($user) {
            return $user->username;
        }

        // 2. Direct match on email
        $userByEmail = User::where('email', $input)->first();
        if ($userByEmail) {
            return $userByEmail->username;
        }

        // 3. Employee ID format (e.g., "00048", "48", "A685-00048", "a68500048")
        $cleaned = preg_replace('/^A?685-?/i', '', $input);
        if (is_numeric($cleaned)) {
            $formatted = 'A685-' . str_pad((string) ((int) $cleaned), 5, '0', STR_PAD_LEFT);
            $userFormatted = User::where('username', $formatted)->first();
            if ($userFormatted) {
                return $userFormatted->username;
            }

            $employee = Employee::where('uid', (int) $cleaned)->with('user')->first();
            if ($employee?->user?->username) {
                return $employee->user->username;
            }
        }

        // 4. Case-insensitive username match
        $userCase = User::whereRaw('LOWER(username) = ?', [strtolower($input)])->first();
        if ($userCase) {
            return $userCase->username;
        }

        return $input;
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();
        $authProvider = $authGuard->getProvider();
        $credentials = $this->getCredentialsFromFormData($data);
        $remember = $data['remember'] ?? false;
        $password = $data['password'] ?? null;
        $timeboxDuration = (int) config('auth.timebox_duration', 200_000);

        $user = app(Timebox::class)->call(function (Timebox $timebox) use ($authProvider, $authGuard, $credentials, $remember): Authenticatable {
            $this->fireAttemptingEvent($authGuard, $credentials, $remember);

            $user = $authProvider->retrieveByCredentials($credentials);

            if ((! $user) || (! $authProvider->validateCredentials($user, $credentials))) {
                $this->userUndertakingMultiFactorAuthentication = null;

                $this->fireFailedEvent($authGuard, $user, $credentials);
                $this->throwFailureValidationException();
            }

            $timebox->returnEarly();

            return $user;
        }, $timeboxDuration);

        // Determine accessible panel
        $currentPanel = Filament::getCurrentOrDefaultPanel();
        $targetPanel = null;

        if ($user instanceof FilamentUser) {
            if ($user->canAccessPanel($currentPanel)) {
                $targetPanel = $currentPanel;
            } else {
                // Check if user has access to another panel (e.g. employee logging in on HR panel)
                foreach (Filament::getPanels() as $panel) {
                    if ($user->canAccessPanel($panel)) {
                        $targetPanel = $panel;
                        break;
                    }
                }

                if (! $targetPanel) {
                    $this->fireFailedEvent($authGuard, $user, $credentials);
                    $this->throwFailureValidationException();
                }
            }
        }

        // Login the user
        $authGuard->login($user, $remember);

        session()->regenerate();

        AccountLog::record($user, 'login');

        if (filled($password) && method_exists($authGuard, 'logoutOtherDevices')) {
            try {
                $authGuard->logoutOtherDevices($password);
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

        // Determine target redirect URL based on user's authorized panel
        $intended = session()->get('url.intended');
        $targetPath = '/' . ltrim($targetPanel ? $targetPanel->getPath() : '', '/');

        $redirectUrl = $targetPanel ? $targetPanel->getUrl() : Filament::getUrl();
        if ($intended && filled($targetPath) && str_contains(parse_url($intended, PHP_URL_PATH) ?? '', $targetPath)) {
            $redirectUrl = $intended;
        }

        session()->forget('url.intended');

        return new class($redirectUrl) implements LoginResponse {
            public function __construct(protected string $url) {}

            public function toResponse($request)
            {
                return redirect()->to($this->url);
            }
        };
    }
}
