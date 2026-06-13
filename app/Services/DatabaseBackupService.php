<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseBackupService
{
    public function download(): StreamedResponse
    {
        $database = DB::connection()->getDatabaseName();
        $filename = 'wdahris-database-backup-'.now()->format('Y-m-d-His').'.sql';

        return response()->streamDownload(function () use ($database): void {
            echo "-- WDA HRIS database backup\n";
            echo '-- Generated: '.now()->toDateTimeString()."\n";
            echo '-- Database: '.$database."\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($this->tableNames() as $table) {
                $this->writeTable($table);
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        }, $filename, [
            'Content-Type' => 'application/sql; charset=UTF-8',
        ]);
    }

    protected function writeTable(string $table): void
    {
        $quotedTable = $this->quoteIdentifier($table);
        $createTable = $this->createTableSql($table);

        echo "DROP TABLE IF EXISTS {$quotedTable};\n";
        echo $createTable.";\n\n";

        foreach (DB::table($table)->cursor() as $row) {
            $data = (array) $row;

            if ($data === []) {
                continue;
            }

            $columns = implode(', ', array_map($this->quoteIdentifier(...), array_keys($data)));
            $values = implode(', ', array_map($this->sqlValue(...), array_values($data)));

            echo "INSERT INTO {$quotedTable} ({$columns}) VALUES ({$values});\n";
        }

        echo "\n";
    }

    protected function tableNames(): array
    {
        return collect(DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"'))
            ->map(fn (object $row): string => (string) array_values((array) $row)[0])
            ->values()
            ->all();
    }

    protected function createTableSql(string $table): string
    {
        $row = (array) DB::selectOne('SHOW CREATE TABLE '.$this->quoteIdentifier($table));

        return (string) array_values($row)[1];
    }

    protected function quoteIdentifier(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }

    protected function sqlValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return DB::connection()->getPdo()->quote((string) $value);
    }
}
