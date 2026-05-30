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
        if (Schema::hasTable('leave_credit_transactions')) {
            return;
        }

        Schema::create('leave_credit_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('particulars')->nullable();
            $table->string('leave_type')->nullable();
            $table->decimal('vacation_earned', 5, 2)->nullable();
            $table->decimal('vacation_absence', 5, 2)->nullable();
            $table->decimal('sick_earned', 5, 2)->nullable();
            $table->decimal('sick_absence', 5, 2)->nullable();
            $table->boolean('is_lwop')->default(false);
            $table->text('action_taken')->nullable();
            $table->timestamps();

            $table->unique(['source_type', 'source_id'], 'leave_credit_source_unique');
            $table->index(['employee_id', 'from_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_credit_transactions');
    }
};
