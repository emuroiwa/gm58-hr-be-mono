<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends BaseModel
{
    protected $fillable = [
        'company_id', 'user_id', 'employee_number', 'first_name', 'last_name',
        'middle_name', 'national_id', 'tax_number', 'passport_number', 'email',
        'phone', 'alternative_phone', 'address', 'city', 'country',
        'position_id', 'department_id', 'manager_id', 'basic_salary',
        'currency_id', 'payment_method', 'payment_schedule', 'bank_name',
        'bank_account', 'bank_branch', 'bank_code', 'swift_code',
        'hire_date', 'probation_end_date', 'contract_end_date',
        'termination_date', 'employment_type', 'employment_status',
        'is_active', 'emergency_contact_name', 'emergency_contact_phone',
        'medical_aid_number', 'medical_aid_provider'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'hire_date' => 'date',
        'probation_end_date' => 'date',
        'contract_end_date' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCompany($query, string $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function isManager(): bool
    {
        return $this->subordinates()->exists();
    }

    public function isOnProbation(): bool
    {
        return $this->probation_end_date && $this->probation_end_date->isFuture();
    }
}
