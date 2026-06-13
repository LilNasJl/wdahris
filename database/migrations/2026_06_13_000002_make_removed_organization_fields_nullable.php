<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->makeColumnNullable('departments', 'description');
        $this->makeColumnNullable('designations', 'description');
        $this->makeColumnNullable('designations', 'specification');
    }

    public function down(): void
    {
        // These fields are no longer collected by the Filament forms.
        // Keep rollback empty to avoid reintroducing insert failures.
    }

    private function makeColumnNullable(string $table, string $column): void
    {
        if (! Schema::hasColumn($table, $column)) {
            return;
        }

        $definition = DB::selectOne(
            'SELECT COLUMN_TYPE, IS_NULLABLE
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = ?
             AND COLUMN_NAME = ?',
            [$table, $column]
        );

        if (! $definition || $definition->IS_NULLABLE === 'YES') {
            return;
        }

        $escapedTable = str_replace('`', '``', $table);
        $escapedColumn = str_replace('`', '``', $column);

        DB::statement("ALTER TABLE `{$escapedTable}` MODIFY `{$escapedColumn}` {$definition->COLUMN_TYPE} NULL");
    }
};
