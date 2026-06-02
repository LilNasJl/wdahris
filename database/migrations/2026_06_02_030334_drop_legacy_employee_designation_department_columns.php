<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('employees', 'designation') && Schema::hasColumn('employees', 'designation_id')) {
            DB::table('employees')
                ->whereNull('designation_id')
                ->whereNotNull('designation')
                ->update(['designation_id' => DB::raw('designation')]);
        }

        if (Schema::hasColumn('employees', 'department') && Schema::hasColumn('employees', 'department_id')) {
            DB::table('employees')
                ->whereNull('department_id')
                ->whereNotNull('department')
                ->update(['department_id' => DB::raw('department')]);
        }

        Schema::table('employees', function (Blueprint $table): void {
            if (Schema::hasColumn('employees', 'designation')) {
                $table->dropColumn('designation');
            }

            if (Schema::hasColumn('employees', 'department')) {
                $table->dropColumn('department');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            if (! Schema::hasColumn('employees', 'designation')) {
                $table->integer('designation')->nullable()->after('email');
            }

            if (! Schema::hasColumn('employees', 'department')) {
                $table->integer('department')->nullable()->after(Schema::hasColumn('employees', 'designation') ? 'designation' : 'designation_id');
            }
        });
    }
};
