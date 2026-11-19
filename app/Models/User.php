<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Throwable;

#[Fillable(['name', 'username', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        $role = strtolower((string) $this->role);
        $isSuperAdmin = $this->isSuperAdminAccount();

        return match ($panel->getId()) {
            'hr' => $isSuperAdmin || $role === 'hr' || $this->hasShieldRole('hr'),
            'personnel' => $role === 'employee' || $this->hasShieldRole('employee'),
            default => false,
        };
    }

    public function canImpersonate(): bool
    {
        return $this->isSuperAdminAccount();
    }

    public function canBeImpersonated(): bool
    {
        if ($this->trashed() || $this->username === 'masteradmin' || auth()->id() === $this->id) {
            return false;
        }

        return strtolower((string) $this->role) !== 'employee'
            && ! $this->hasShieldRole('employee');
    }

    public function getFilamentName(): string
    {
        return $this->employee?->full_name
            ?? $this->username
            ?? $this->name
            ?? $this->email
            ?? 'User';
    }

    protected function hasShieldRole(string $role): bool
    {
        try {
            return $this->hasRole($role);
        } catch (Throwable) {
            return false;
        }
    }

    protected function isSuperAdminAccount(): bool
    {
        return strtolower((string) $this->role) === 'superadmin'
            || $this->hasShieldRole(config('filament-shield.super_admin.name', 'super_admin'));
    }

    protected static function booted(): void
    {
        static::deleting(function (User $user): void {
            if ($user->isForceDeleting()) {
                $user->employee()->withTrashed()->forceDelete();

                return;
            }

            $user->employee()->delete();
        });

        static::restoring(function (User $user): void {
            $user->employee()->withTrashed()->restore();
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAuthPassword(): ?string
    {
        $password = $this->password;

        if (is_string($password) && preg_match('/^\$\d{2}\$/', $password)) {
            return '$2y'.$password;
        }

        return $password;
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id')->withTrashed();
    }

    public function deduction()
    {
        return $this->hasMany(Deduction::class);
    }
}
