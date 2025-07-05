<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'base_salary',
        'hourly_rate',
        'overtime_rate',
        'currency',
        'pay_frequency',
        'effective_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAnnualSalary(): float
    {
        return match($this->pay_frequency) {
            'weekly' => $this->base_salary * 52,
            'bi-weekly' => $this->base_salary * 26,
            'monthly' => $this->base_salary * 12,
            'quarterly' => $this->base_salary * 4,
            'annually' => $this->base_salary,
            default => $this->base_salary * 12,
        };
    }
}
