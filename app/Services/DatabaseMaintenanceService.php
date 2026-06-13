<?php

namespace App\Services;

use App\Models\ActionLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DatabaseMaintenanceService
{
    public function clearOperationalData(): array
    {
        $tables = $this->clearableTables();
        $counts = [];

        $employeeAccountsDeleted = $this->deleteEmployeeAccounts();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($tables as $table) {
                $counts[$table] = DB::table($table)->count();
                DB::statement('TRUNCATE TABLE '.$this->quoteIdentifier($table));
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        ActionLog::recordSystemAction('database_cleared', 'Operational database tables were cleared.', [
            'tables' => $tables,
            'deleted_records' => array_sum($counts) + $employeeAccountsDeleted,
            'table_counts' => $counts,
            'employee_accounts_deleted' => $employeeAccountsDeleted,
            'preserved_tables' => $this->preservedTables(),
        ]);

        return [
            'tables' => $tables,
            'deleted_records' => array_sum($counts) + $employeeAccountsDeleted,
        ];
    }

    protected function deleteEmployeeAccounts(): int
    {
        if (! in_array('users', $this->tableNames(), true)) {
            return 0;
        }

        $employeeUserIds = DB::table('users')
            ->where('role', 'employee')
            ->pluck('id')
            ->all();

        if ($employeeUserIds === []) {
            return 0;
        }

        DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->whereIn('model_id', $employeeUserIds)
            ->delete();

        DB::table('model_has_permissions')
            ->where('model_type', User::class)
            ->whereIn('model_id', $employeeUserIds)
            ->delete();

        return DB::table('users')
            ->whereIn('id', $employeeUserIds)
            ->delete();
    }

    protected function clearableTables(): array
    {
        return collect($this->tableNames())
            ->reject(fn (string $table): bool => in_array($table, $this->preservedTables(), true))
            ->values()
            ->all();
    }

    protected function preservedTables(): array
    {
        return [
            'migrations',
            'users',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'sessions',
            'password_reset_tokens',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'account_logs',
            'action_logs',
        ];
    }

    protected function tableNames(): array
    {
        return collect(DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"'))
            ->map(fn (object $row): string => (string) array_values((array) $row)[0])
            ->values()
            ->all();
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }
}
