<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('training_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['enrolled', 'completed', 'failed', 'dropped'])->default('enrolled');
            $table->date('enrolled_at')->default(now());
            $table->date('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->unique(['training_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_employee');
    }
};
