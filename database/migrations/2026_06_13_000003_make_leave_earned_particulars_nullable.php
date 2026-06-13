<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('leave_credit_earneds', 'particulars')) {
            DB::statement('ALTER TABLE leave_credit_earneds MODIFY particulars VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('leave_credit_earneds', 'particulars')) {
            DB::table('leave_credit_earneds')
                ->whereNull('particulars')
                ->update(['particulars' => '']);

            DB::statement('ALTER TABLE leave_credit_earneds MODIFY particulars VARCHAR(255) NOT NULL');
        }
    }
};
