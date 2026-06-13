<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AccountLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'account_name',
        'username',
        'email',
        'role',
        'event',
        'panel',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }

    public static function record(?Authenticatable $user, string $event): void
    {
        if (! $user instanceof User || ! Schema::hasTable('account_logs')) {
            return;
        }

        try {
            static::withoutEvents(function () use ($user, $event): void {
                static::query()->create([
                    'user_id' => $user->id,
                    'account_name' => $user->getFilamentName(),
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->roles()->pluck('name')->implode(', ') ?: $user->role,
                    'event' => $event,
                    'panel' => Filament::getCurrentPanel()?->getId(),
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'occurred_at' => now(),
                ]);
            });
        } catch (Throwable) {
            //
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
