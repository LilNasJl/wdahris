<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultSystemAccountsSeeder extends Seeder
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

        foreach ($this->defaultAccounts() as $account) {
            $user = User::query()->updateOrCreate(
                ['username' => $account['username']],
                [
                    'name' => $account['username'],
                    'email' => $account['email'],
                    'password' => Hash::make($account['password']),
                    'role' => 'superadmin',
                ],
            );

            $user->syncRoles([$superAdminRole]);
        }
    }

    protected function defaultAccounts(): array
    {
        return [
            [
                'username' => 'superadmin',
                'password' => 'superadmin',
                'email' => 'superadmin@example.com',
            ],
            [
                'username' => 'masteradmin',
                'password' => 'masteradmin',
                'email' => 'masteradmin@example.com',
            ],
        ];
    }
}
