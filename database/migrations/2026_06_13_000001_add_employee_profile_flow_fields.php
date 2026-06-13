<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            if (! Schema::hasColumn('employees', 'name_extension')) {
                $table->string('name_extension', 20)->nullable()->after('lastname');
            }

            if (! Schema::hasColumn('employees', 'place_of_birth')) {
                $table->string('place_of_birth')->nullable()->after('birthdate');
            }

            if (! Schema::hasColumn('employees', 'citizenship')) {
                $table->string('citizenship', 100)->nullable()->after('status');
            }

            if (! Schema::hasColumn('employees', 'blood_type')) {
                $table->string('blood_type', 40)->nullable()->after('citizenship');
            }

            if (! Schema::hasColumn('employees', 'height_cm')) {
                $table->decimal('height_cm', 6, 2)->nullable()->after('blood_type');
            }

            if (! Schema::hasColumn('employees', 'weight_kg')) {
                $table->decimal('weight_kg', 6, 2)->nullable()->after('height_cm');
            }

            if (! Schema::hasColumn('employees', 'residential_address')) {
                $table->text('residential_address')->nullable()->after('email');
            }

            if (! Schema::hasColumn('employees', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('residential_address');
            }

            if (! Schema::hasColumn('employees', 'philsys')) {
                $table->string('philsys', 40)->nullable()->after('tin');
            }

            if (! Schema::hasColumn('employees', 'salary_grade')) {
                $table->unsignedTinyInteger('salary_grade')->nullable()->after('employment_type');
            }

            if (! Schema::hasColumn('employees', 'salary_step')) {
                $table->unsignedTinyInteger('salary_step')->nullable()->after('salary_grade');
            }

            if (! Schema::hasColumn('employees', 'original_appointment_date')) {
                $table->date('original_appointment_date')->nullable()->after('hired_date');
            }

            if (! Schema::hasColumn('employees', 'assumption_date')) {
                $table->date('assumption_date')->nullable()->after('original_appointment_date');
            }

            if (! Schema::hasColumn('employees', 'employment_start_date')) {
                $table->date('employment_start_date')->nullable()->after('assumption_date');
            }

            if (! Schema::hasColumn('employees', 'employment_end_date')) {
                $table->date('employment_end_date')->nullable()->after('employment_start_date');
            }

            if (! Schema::hasColumn('employees', 'immediate_supervisor')) {
                $table->string('immediate_supervisor')->nullable()->after('employment_end_date');
            }

            if (! Schema::hasColumn('employees', 'employment_classification')) {
                $table->string('employment_classification')->nullable()->after('immediate_supervisor');
            }

            if (! Schema::hasColumn('employees', 'plantilla_item_number')) {
                $table->string('plantilla_item_number')->nullable()->after('employment_classification');
            }

            if (! Schema::hasColumn('employees', 'office_assignment')) {
                $table->string('office_assignment')->nullable()->after('plantilla_item_number');
            }

            if (! Schema::hasColumn('employees', 'employment_remarks')) {
                $table->text('employment_remarks')->nullable()->after('office_assignment');
            }
        });

        if (! Schema::hasTable('employee_educations')) {
            Schema::create('employee_educations', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->string('school_name', 250);
                $table->string('school_level');
                $table->date('year_grad')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_educations');

        Schema::table('employees', function (Blueprint $table): void {
            foreach ([
                'name_extension',
                'place_of_birth',
                'citizenship',
                'blood_type',
                'height_cm',
                'weight_kg',
                'residential_address',
                'permanent_address',
                'philsys',
                'salary_grade',
                'salary_step',
                'original_appointment_date',
                'assumption_date',
                'employment_start_date',
                'employment_end_date',
                'immediate_supervisor',
                'employment_classification',
                'plantilla_item_number',
                'office_assignment',
                'employment_remarks',
            ] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
