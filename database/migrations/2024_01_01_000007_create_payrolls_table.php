<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('payroll_period_id')->constrained()->cascadeOnDelete();
            
            // Earnings
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('overtime_amount', 12, 2)->default(0);
            $table->decimal('bonus_amount', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('gross_pay', 12, 2)->default(0);
            
            // Deductions
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            
            // Work Information
            $table->integer('working_days')->default(0);
            $table->integer('worked_days')->default(0);
            $table->decimal('worked_hours', 8, 2)->default(0);
            
            // Status
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'payroll_period_id']);
            $table->index(['employee_id', 'payroll_period_id']);
            $table->unique(['employee_id', 'payroll_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
