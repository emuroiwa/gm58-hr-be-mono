<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->unique();
            
            // Tax Settings
            $table->boolean('enable_paye')->default(true);
            $table->boolean('enable_nssa')->default(true);
            $table->boolean('enable_aids_levy')->default(true);
            $table->json('custom_tax_rates')->nullable();
            
            // Leave Settings
            $table->date('leave_year_start')->nullable();
            $table->boolean('allow_negative_leave')->default(false);
            
            // Payroll Settings
            $table->integer('payroll_approval_levels')->default(1);
            $table->boolean('require_timesheet')->default(false);
            
            // Notification Settings
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
