<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class ActionLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'actor_name',
        'actor_username',
        'action',
        'model_type',
        'model_label',
        'record_id',
        'record_label',
        'description',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public static function recordModelEvent(string $eventName, Model $model): void
    {
        if (! static::canRecord($model)) {
            return;
        }

        $action = Str::of($eventName)
            ->after('eloquent.')
            ->before(':')
            ->replace('forceDeleted', 'force_deleted')
            ->toString();

        [$oldValues, $newValues] = static::eventValues($action, $model);

        static::record([
            'action' => $action,
            'model_type' => $model::class,
            'model_label' => Str::headline(class_basename($model)),
            'record_id' => (string) $model->getKey(),
            'record_label' => static::recordLabel($model),
            'description' => Str::headline($action).' '.Str::headline(class_basename($model)),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    public static function recordSystemAction(string $action, string $description, array $details = []): void
    {
        static::record([
            'action' => $action,
            'model_type' => 'system',
            'model_label' => 'System',
            'record_label' => 'Database Management',
            'description' => $description,
            'new_values' => $details,
        ]);
    }

    protected static function record(array $data): void
    {
        if (! Schema::hasTable('action_logs')) {
            return;
        }

        try {
            $user = Filament::auth()?->user() ?? Auth::user();

            static::withoutEvents(function () use ($data, $user): void {
                static::query()->create([
                    ...$data,
                    'user_id' => $user?->id,
                    'actor_name' => $user?->getFilamentName(),
                    'actor_username' => $user?->username,
                    'url' => Request::fullUrl(),
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'occurred_at' => now(),
                ]);
            });
        } catch (Throwable) {
            //
        }
    }

    protected static function canRecord(Model $model): bool
    {
        if (! Schema::hasTable('action_logs')) {
            return false;
        }

        return ! in_array($model::class, [
            self::class,
            AccountLog::class,
        ], true);
    }

    protected static function eventValues(string $action, Model $model): array
    {
        $attributes = static::filteredAttributes($model->getAttributes());

        if ($action === 'created' || $action === 'restored') {
            return [null, $attributes];
        }

        if ($action === 'deleted' || $action === 'force_deleted') {
            return [$attributes, null];
        }

        $changes = static::filteredAttributes($model->getChanges());
        $oldValues = [];

        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        return [
            static::filteredAttributes($oldValues),
            $changes,
        ];
    }

    protected static function filteredAttributes(array $attributes): array
    {
        foreach (['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'] as $field) {
            unset($attributes[$field]);
        }

        return $attributes;
    }

    protected static function recordLabel(Model $model): string
    {
        foreach (['full_name', 'name', 'title', 'username', 'email', 'uid', 'id'] as $attribute) {
            if (filled($model->{$attribute} ?? null)) {
                return (string) $model->{$attribute};
            }
        }

        return (string) $model->getKey();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
