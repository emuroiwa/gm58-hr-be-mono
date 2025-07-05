<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('leave_type_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days');
            $table->text('reason');
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->json('attachments')->nullable();
            
            // Status and Approval
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('applied_at');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->uuid('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('comments')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['employee_id', 'status']);
            $table->index(['employee_id', 'start_date', 'end_date']);
            
            $table->foreign('approved_by')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('rejected_by')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
