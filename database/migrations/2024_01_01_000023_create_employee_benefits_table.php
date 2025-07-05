<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('benefit_id')->constrained()->cascadeOnDelete();
            $table->date('enrolled_at');
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->decimal('employee_contribution', 8, 2)->nullable();
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->timestamps();
            
            $table->unique(['employee_id', 'benefit_id']);
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_benefits');
    }
};
