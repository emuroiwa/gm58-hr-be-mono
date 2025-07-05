<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('break_duration')->default(0); // in minutes
            $table->decimal('worked_hours', 8, 2)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['employee_id', 'date']);
            $table->index(['employee_id', 'date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
