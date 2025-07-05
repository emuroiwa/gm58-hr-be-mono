<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends BaseModel
{
    protected $fillable = [
        'name', 'code', 'email', 'phone', 'address', 'city', 'country',
        'website', 'tax_number', 'registration_no', 'industry', 'size',
        'base_currency_id', 'billing_plan', 'billing_cycle', 'subscription_end',
        'max_employees', 'logo_url', 'payroll_cycle', 'work_week_days',
        'work_day_hours', 'overtime_rate', 'weekend_rate', 'is_active',
        'is_verified', 'verified_at', 'created_by'
    ];

    protected $casts = [
        'subscription_end' => 'datetime',
        'verified_at' => 'datetime',
        'work_day_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'weekend_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'max_employees' => 'integer',
        'work_week_days' => 'integer',
    ];

    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function payrollPeriods(): HasMany
    {
        return $this->hasMany(PayrollPeriod::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(CompanySettings::class);
    }

    public function isActive(): bool
    {
        return $this->is_active && (!$this->subscription_end || $this->subscription_end->isFuture());
    }

    public function canAddEmployees(): bool
    {
        return $this->employees()->count() < $this->max_employees;
    }

    public function getActiveEmployeesCount(): int
    {
        return $this->employees()->where('is_active', true)->count();
    }
}
