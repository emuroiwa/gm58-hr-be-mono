<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->integer('year');
            $table->integer('month');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('draft'); // draft, processing, processed, approved, paid
            $table->text('description')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->uuid('processed_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('processed_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            
            $table->unique(['company_id', 'year', 'month']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
