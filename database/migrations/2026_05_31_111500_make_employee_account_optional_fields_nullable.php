<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            foreach (['middlename', 'gender', 'status', 'address', 'mobile', 'email', 'employment_type', 'school_name', 'school_level', 'rate_type', 'payment_type'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->string($column)->nullable()->change();
                }
            }

            foreach (['philhealth', 'gsis', 'sss', 'pagibig', 'tin'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->string($column, 20)->nullable()->change();
                }
            }

            if (Schema::hasColumn('employees', 'kids')) {
                $table->smallInteger('kids')->nullable()->change();
            }

            if (Schema::hasColumn('employees', 'year_grad')) {
                $table->date('year_grad')->nullable()->change();
            }

            foreach (['daily_rate', 'monthly_rate', 'allowance'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->double($column)->nullable()->change();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            foreach (['middlename', 'gender', 'status', 'address', 'mobile', 'email', 'employment_type', 'school_name', 'school_level', 'rate_type', 'payment_type'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->string($column)->nullable(false)->change();
                }
            }

            foreach (['philhealth', 'gsis', 'sss', 'pagibig', 'tin'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->string($column, 20)->nullable(false)->change();
                }
            }

            if (Schema::hasColumn('employees', 'kids')) {
                $table->smallInteger('kids')->nullable(false)->change();
            }

            if (Schema::hasColumn('employees', 'year_grad')) {
                $table->date('year_grad')->nullable(false)->change();
            }

            foreach (['daily_rate', 'monthly_rate', 'allowance'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->double($column)->nullable(false)->change();
                }
            }
        });
    }
};
