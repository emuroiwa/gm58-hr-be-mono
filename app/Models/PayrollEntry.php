<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollEntry extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'payroll_period_id',
        'employee_id',
        'base_salary',
        'overtime_hours',
        'overtime_pay',
        'bonus',
        'commission',
        'gross_pay',
        'total_deductions',
        'total_taxes',
        'net_pay',
        'hours_worked',
        'status',
        'pay_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'pay_date' => 'date',
    ];

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class);
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(PayrollTax::class);
    }

    public function calculateGrossPay(): float
    {
        return $this->base_salary + $this->overtime_pay + $this->bonus + $this->commission;
    }

    public function calculateNetPay(): float
    {
        return $this->gross_pay - $this->total_deductions - $this->total_taxes;
    }
}
