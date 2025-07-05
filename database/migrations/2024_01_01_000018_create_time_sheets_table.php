<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('break_duration')->default(0); // in minutes
            $table->integer('duration')->nullable(); // in minutes
            $table->text('description')->nullable();
            $table->string('project')->nullable();
            $table->string('task')->nullable();
            $table->boolean('billable')->default(false);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['employee_id', 'date']);
            $table->index(['employee_id', 'status']);
            
            $table->foreign('approved_by')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_sheets');
    }
};
