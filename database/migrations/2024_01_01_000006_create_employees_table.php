<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_id')->nullable()->unique();
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('avatar')->nullable();
            
            // Address Information
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            
            // Employment Information
            $table->date('hire_date');
            $table->foreignUuid('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('position_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('manager_id')->nullable();
            $table->string('job_title');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            
            // Compensation
            $table->decimal('salary', 12, 2)->nullable();
            $table->foreignId('currency_id')->default(1)->constrained();
            
            // Calculated fields
            $table->string('full_name')->nullable();
            $table->integer('years_of_service')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'department_id']);
            $table->index(['company_id', 'manager_id']);
            $table->index('employee_id');
            
            $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
