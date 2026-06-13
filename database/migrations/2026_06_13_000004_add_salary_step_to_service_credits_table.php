<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_credits', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_credits', 'salary_step')) {
                $table->unsignedTinyInteger('salary_step')->nullable()->after('designation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_credits', function (Blueprint $table): void {
            if (Schema::hasColumn('service_credits', 'salary_step')) {
                $table->dropColumn('salary_step');
            }
        });
    }
};
