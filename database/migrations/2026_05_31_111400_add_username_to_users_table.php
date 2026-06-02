<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('username')->nullable()->unique();
            });
        }

        DB::table('users')
            ->whereNull('username')
            ->orWhere('username', '')
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->each(function (object $user): void {
                $base = Str::slug($user->name ?: Str::before((string) $user->email, '@') ?: 'user-'.$user->id, '_');
                $base = $base ?: 'user_'.$user->id;
                $username = $base;
                $suffix = 1;

                while (
                    DB::table('users')
                        ->where('username', $username)
                        ->where('id', '!=', $user->id)
                        ->exists()
                ) {
                    $username = $base.'_'.++$suffix;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $username]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('username');
            });
        }
    }
};
