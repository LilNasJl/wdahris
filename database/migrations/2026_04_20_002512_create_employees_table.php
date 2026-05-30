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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');

            // personal information
            $table->string('firstname');
            $table->string('middlename');
            $table->string('lastname');
            $table->string('gender');
            $table->date('birthdate');
            $table->string('status');
            $table->string('address');
            $table->string('mobile');
            $table->smallInteger('kids');
            $table->string('email');

            // designation
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->date('hired_date');
            $table->string('employment_type');

            // education
            $table->string('school_name');
            $table->string('school_level');
            $table->date('year_grad');

            // salary
            $table->string('rate_type');
            $table->string('payment_type');
            $table->double('daily_rate');
            $table->double('monthly_rate');
            $table->double('allowance');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
