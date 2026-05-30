<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('service_credits', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_credits', 'employee_id')) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('employees')
                    ->cascadeOnDelete();
            }

            if (! Schema::hasColumn('service_credits', 'from_date')) {
                $table->date('from_date')->nullable()->after('employee_id');
            }

            if (! Schema::hasColumn('service_credits', 'to_date')) {
                $table->date('to_date')->nullable()->after('from_date');
            }

            if (! Schema::hasColumn('service_credits', 'designation')) {
                $table->string('designation')->nullable()->after('to_date');
            }

            if (! Schema::hasColumn('service_credits', 'status')) {
                $table->string('status')->nullable()->after('designation');
            }

            if (! Schema::hasColumn('service_credits', 'salary')) {
                $table->decimal('salary', 12, 2)->nullable()->after('status');
            }

            if (! Schema::hasColumn('service_credits', 'station_place')) {
                $table->string('station_place')->nullable()->after('salary');
            }

            if (! Schema::hasColumn('service_credits', 'branch')) {
                $table->string('branch')->nullable()->after('station_place');
            }

            if (! Schema::hasColumn('service_credits', 'leave_without_pay')) {
                $table->string('leave_without_pay')->nullable()->after('branch');
            }

            if (! Schema::hasColumn('service_credits', 'separation_date')) {
                $table->date('separation_date')->nullable()->after('leave_without_pay');
            }

            if (! Schema::hasColumn('service_credits', 'separation_case')) {
                $table->string('separation_case')->nullable()->after('separation_date');
            }

            if (! Schema::hasColumn('service_credits', 'remarks')) {
                $table->text('remarks')->nullable()->after('separation_case');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_credits', function (Blueprint $table): void {
            foreach ([
                'remarks',
                'separation_case',
                'separation_date',
                'leave_without_pay',
                'branch',
                'station_place',
                'salary',
                'status',
                'designation',
                'to_date',
                'from_date',
            ] as $column) {
                if (Schema::hasColumn('service_credits', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('service_credits', 'employee_id')) {
                $table->dropConstrainedForeignId('employee_id');
            }
        });
    }
};
