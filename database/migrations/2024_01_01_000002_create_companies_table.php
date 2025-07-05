<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            
            // Address Information
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            
            // Business Information
            $table->string('tax_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('timezone')->default('UTC');
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            
            // Subscription & Limits
            $table->boolean('is_active')->default(true);
            $table->string('subscription_status')->default('active');
            $table->integer('employee_limit')->nullable();
            
            // Settings (JSON)
            $table->json('settings')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_active', 'subscription_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
