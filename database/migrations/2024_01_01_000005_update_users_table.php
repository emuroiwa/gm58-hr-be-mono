<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change id to UUID if not already
            if (Schema::hasColumn('users', 'id') && Schema::getColumnType('users', 'id') !== 'string') {
                $table->dropPrimary(['id']);
                $table->uuid('id')->primary()->change();
            }
            
            // Add company and employee relationships
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->foreignUuid('company_id')->nullable()->constrained()->cascadeOnDelete();
            }
            
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->uuid('employee_id')->nullable();
            }
            
            // Add role and status
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'admin', 'hr', 'manager', 'employee'])->default('employee');
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            
            // Add soft deletes if not exists
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
            
            $table->index(['company_id', 'role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'employee_id', 'role', 'is_active', 'last_login_at']);
            $table->dropSoftDeletes();
        });
    }
};
