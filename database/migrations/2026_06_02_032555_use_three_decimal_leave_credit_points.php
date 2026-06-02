<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('leave_credit_earneds', 'earned')) {
            DB::statement('ALTER TABLE leave_credit_earneds MODIFY earned DECIMAL(6, 3) NOT NULL');
        }

        if (! Schema::hasTable('leave_credit_transactions')) {
            return;
        }

        foreach (['vacation_earned', 'vacation_absence', 'sick_earned', 'sick_absence'] as $column) {
            if (Schema::hasColumn('leave_credit_transactions', $column)) {
                DB::statement("ALTER TABLE leave_credit_transactions MODIFY `{$column}` DECIMAL(6, 3) NULL");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('leave_credit_earneds', 'earned')) {
            DB::statement('ALTER TABLE leave_credit_earneds MODIFY earned DECIMAL(5, 2) NOT NULL');
        }

        if (! Schema::hasTable('leave_credit_transactions')) {
            return;
        }

        foreach (['vacation_earned', 'vacation_absence', 'sick_earned', 'sick_absence'] as $column) {
            if (Schema::hasColumn('leave_credit_transactions', $column)) {
                DB::statement("ALTER TABLE leave_credit_transactions MODIFY `{$column}` DECIMAL(5, 2) NULL");
            }
        }
    }
};
