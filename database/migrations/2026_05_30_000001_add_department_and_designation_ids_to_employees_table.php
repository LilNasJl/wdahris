<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('employees', 'designation_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('designation_id')
                    ->nullable()
                    ->after(Schema::hasColumn('employees', 'designation') ? 'designation' : 'email');
            });
        }

        if (! Schema::hasColumn('employees', 'department_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('department_id')
                    ->nullable()
                    ->after(Schema::hasColumn('employees', 'department') ? 'department' : 'designation_id');
            });
        }

        if (Schema::hasColumn('employees', 'designation')) {
            DB::table('employees')
                ->whereNull('designation_id')
                ->update(['designation_id' => DB::raw('designation')]);
        }

        if (Schema::hasColumn('employees', 'department')) {
            DB::table('employees')
                ->whereNull('department_id')
                ->update(['department_id' => DB::raw('department')]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'department_id')) {
                $table->dropColumn('department_id');
            }

            if (Schema::hasColumn('employees', 'designation_id')) {
                $table->dropColumn('designation_id');
            }
        });
    }
};
