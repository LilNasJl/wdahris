<?php

namespace App\Http\Middleware;

use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

class RedirectToUnifiedLogin extends FilamentAuthenticate
{
    protected function redirectTo($request): ?string
    {
        return route('filament.hr.auth.login');
    }
}
