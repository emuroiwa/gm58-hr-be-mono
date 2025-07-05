<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('employee_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->uuid('currency_id');
            $table->boolean('is_fixed')->default(true);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->boolean('is_statutory')->default(false);
            $table->boolean('is_recurring')->default(true);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('currency_id')->references('id')->on('currencies');
            
            $table->index(['company_id', 'employee_id']);
            $table->index(['company_id', 'is_active', 'is_recurring']);
            $table->index(['company_id', 'is_statutory']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
