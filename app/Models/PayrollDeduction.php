<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDeduction extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $table = 'payroll_deductions';

    protected $fillable = [
        'company_id',
        'payroll_id',
        'payroll_entry_id',
        'employee_id',
        'deduction_type',
        'name',
        'amount',
        'percentage',
        'is_taxable',
        'is_pre_tax',
        'description',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:4',
        'is_taxable' => 'boolean',
        'is_pre_tax' => 'boolean',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function payrollEntry(): BelongsTo
    {
        return $this->belongsTo(PayrollEntry::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateAmount(float $grossPay): float
    {
        if ($this->amount > 0) {
            return $this->amount;
        }

        if ($this->percentage > 0) {
            return $grossPay * ($this->percentage / 100);
        }

        return 0;
    }

    public function scopePreTax($query)
    {
        return $query->where('is_pre_tax', true);
    }

    public function scopePostTax($query)
    {
        return $query->where('is_pre_tax', false);
    }

    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}