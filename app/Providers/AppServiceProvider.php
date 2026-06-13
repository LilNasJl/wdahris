<?php

namespace App\Providers;

use App\Filament\Resources\SystemAccounts\SystemAccountResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\AccountLog;
use App\Models\ActionLog;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentShield::buildPermissionKeyUsing(function (string $entity, string $affix, string $subject, string $case, string $separator): string {
            if (in_array($entity, [UserResource::class, SystemAccountResource::class], true)) {
                $subject = class_basename($entity);
            }

            return $this->formatShieldPermissionPart($affix, $case)
                .$separator
                .$this->formatShieldPermissionPart($subject, $case);
        });

        $this->registerAuditLogListeners();
    }

    protected function registerAuditLogListeners(): void
    {
        Event::listen(Logout::class, function (Logout $event): void {
            AccountLog::record($event->user, 'logout');
        });

        foreach (['created', 'updated', 'deleted', 'restored', 'forceDeleted'] as $event) {
            Event::listen("eloquent.{$event}: *", function (string $eventName, array $payload): void {
                $model = $payload[0] ?? null;

                if ($model instanceof Model) {
                    ActionLog::recordModelEvent($eventName, $model);
                }
            });
        }
    }

    protected function formatShieldPermissionPart(string $value, string $case): string
    {
        return match ($case) {
            'kebab' => Str::of($value)->kebab()->toString(),
            'pascal' => Str::of($value)->studly()->toString(),
            'upper_snake' => Str::of($value)->snake()->upper()->toString(),
            'lower_snake' => Str::of($value)->snake()->lower()->toString(),
            'camel' => Str::of($value)->camel()->toString(),
            default => Str::of($value)->snake()->toString(),
        };
    }
}
