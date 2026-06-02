<?php

namespace App\Filament\Resources\Concerns;

use BackedEnum;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

trait AuthorizesResourceShieldPermissions
{
    public static function canAccess(): bool
    {
        return static::canViewAny();
    }

    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        if (static::shouldSkipAuthorization()) {
            return Response::allow();
        }

        return static::canUseShieldPermission(static::normalizeShieldAction($action))
            ? Response::allow()
            : Response::deny();
    }

    public static function can(string|UnitEnum $action, ?Model $record = null): bool
    {
        return static::getAuthorizationResponse($action, $record)->allowed();
    }

    public static function canViewAny(): bool
    {
        return static::canUseShieldPermission('viewAny');
    }

    public static function canCreate(): bool
    {
        return static::canUseShieldPermission('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::canUseShieldPermission('update');
    }

    public static function canDelete(Model $record): bool
    {
        return static::canUseShieldPermission('delete');
    }

    public static function canDeleteAny(): bool
    {
        return static::canUseShieldPermission('deleteAny');
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canUseShieldPermission('forceDelete');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canUseShieldPermission('forceDeleteAny');
    }

    public static function canReorder(): bool
    {
        return static::canUseShieldPermission('reorder');
    }

    public static function canReplicate(Model $record): bool
    {
        return static::canUseShieldPermission('replicate');
    }

    public static function canRestore(Model $record): bool
    {
        return static::canUseShieldPermission('restore');
    }

    public static function canRestoreAny(): bool
    {
        return static::canUseShieldPermission('restoreAny');
    }

    public static function canView(Model $record): bool
    {
        return static::canUseShieldPermission('view');
    }

    protected static function canUseShieldPermission(string $action): bool
    {
        $user = Filament::auth()?->user();

        return (bool) $user?->can(static::getShieldPermissionName($action));
    }

    protected static function normalizeShieldAction(string|UnitEnum $action): string
    {
        if ($action instanceof BackedEnum) {
            return (string) $action->value;
        }

        if ($action instanceof UnitEnum) {
            return $action->name;
        }

        return $action;
    }

    protected static function getShieldPermissionName(string $action): string
    {
        $case = (string) config('filament-shield.permissions.case', 'pascal');
        $separator = (string) config('filament-shield.permissions.separator', ':');

        return static::formatShieldPermissionPart($action, $case)
            .$separator
            .static::formatShieldPermissionPart(class_basename(static::class), $case);
    }

    protected static function formatShieldPermissionPart(string $value, string $case): string
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
