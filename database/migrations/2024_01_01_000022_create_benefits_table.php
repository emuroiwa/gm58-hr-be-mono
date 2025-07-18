<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['health', 'dental', 'vision', 'retirement', 'life_insurance', 'other']);
            $table->decimal('company_contribution', 8, 2)->nullable();
            $table->decimal('employee_contribution', 8, 2)->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefits');
    }
};
