<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShieldSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DefaultSystemAccountsSeeder::class);
    }
}
