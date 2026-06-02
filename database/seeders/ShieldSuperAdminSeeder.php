<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ShieldSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';
        $superAdminRole = config('filament-shield.super_admin.name', 'super_admin');

        Role::findOrCreate($superAdminRole, $guard);
        Role::findOrCreate('hr', $guard);
        Role::findOrCreate('employee', $guard);

        Artisan::call('shield:generate', [
            '--all' => true,
            '--option' => 'permissions',
            '--panel' => 'hr',
        ]);

        $user = User::query()->updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'superadmin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('superadmin'),
                'role' => 'superadmin',
            ],
        );

        $user->syncRoles([$superAdminRole]);
    }
}
