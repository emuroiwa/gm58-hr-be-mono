<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('employee_id');
            $table->uuid('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_requested');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('leave_type_id')->references('id')->on('leave_types');
            $table->foreign('approved_by')->references('id')->on('employees');
            
            $table->index(['company_id', 'employee_id']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
