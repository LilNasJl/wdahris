<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->makeEmployeeColumnNullable('designation');
        $this->makeEmployeeColumnNullable('department');
    }

    public function down(): void
    {
        // These legacy columns are no longer written by the employee account form.
        // Keeping the rollback empty avoids reintroducing insert failures.
    }

    private function makeEmployeeColumnNullable(string $column): void
    {
        if (! Schema::hasColumn('employees', $column)) {
            return;
        }

        $definition = DB::selectOne(
            "SELECT COLUMN_TYPE, IS_NULLABLE
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = 'employees'
             AND COLUMN_NAME = ?",
            [$column]
        );

        if (! $definition || $definition->IS_NULLABLE === 'YES') {
            return;
        }

        $escapedColumn = str_replace('`', '``', $column);

        DB::statement("ALTER TABLE employees MODIFY `{$escapedColumn}` {$definition->COLUMN_TYPE} NULL");
    }
};
