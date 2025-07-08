<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $table = 'payrolls';

    protected $fillable = [
        'company_id',
        'payroll_period_id',
        'employee_id',
        'currency_id',
        'base_salary',
        'overtime_hours',
        'overtime_pay',
        'bonus',
        'commission',
        'gross_salary',
        'total_deductions',
        'total_taxes',
        'net_salary',
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
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'net_salary' => 'decimal:2',
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

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
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
        return $this->gross_salary - $this->total_deductions - $this->total_taxes;
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }
}