<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->uuid('reviewer_id');
            $table->date('review_period_start');
            $table->date('review_period_end');
            
            // Review Content
            $table->text('goals')->nullable();
            $table->text('achievements')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('goals_next_period')->nullable();
            $table->text('comments')->nullable();
            
            // Ratings (1-5 scale)
            $table->integer('overall_rating')->nullable();
            $table->integer('technical_skills')->nullable();
            $table->integer('communication_skills')->nullable();
            $table->integer('teamwork')->nullable();
            $table->integer('leadership')->nullable();
            $table->integer('punctuality')->nullable();
            
            // Status and Approval
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['employee_id', 'review_period_start']);
            $table->index(['reviewer_id', 'status']);
            
            $table->foreign('reviewer_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('approved_by')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
