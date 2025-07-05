<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('employee_id');
            $table->uuid('payroll_period_id');
            $table->uuid('currency_id');
            $table->decimal('exchange_rate', 15, 6)->default(1);
            
            // Earnings (in employee's currency)
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('overtime', 15, 2)->default(0);
            $table->decimal('allowances', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('other_earnings', 15, 2)->default(0);
            $table->decimal('total_earnings', 15, 2);
            
            // Deductions (in employee's currency)
            $table->decimal('payee_tax', 15, 2)->default(0);
            $table->decimal('aids_levy', 15, 2)->default(0);
            $table->decimal('nssa_contribution', 15, 2)->default(0);
            $table->decimal('pension_contribution', 15, 2)->default(0);
            $table->decimal('medical_aid', 15, 2)->default(0);
            $table->decimal('union_dues', 15, 2)->default(0);
            $table->decimal('loan_deductions', 15, 2)->default(0);
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2);
            
            // Net Pay (in employee's currency)
            $table->decimal('net_pay', 15, 2);
            
            // Base Currency Amounts (for reporting)
            $table->decimal('total_earnings_base', 15, 2);
            $table->decimal('total_deductions_base', 15, 2);
            $table->decimal('net_pay_base', 15, 2);
            
            // Working Days
            $table->integer('working_days');
            $table->integer('days_worked');
            $table->integer('days_absent');
            
            // Status
            $table->string('status')->default('generated'); // generated, approved, paid
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');
            $table->foreign('currency_id')->references('id')->on('currencies');
            
            $table->unique(['company_id', 'employee_id', 'payroll_period_id']);
            $table->index(['company_id', 'payroll_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
