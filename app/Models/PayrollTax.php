<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollTax extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $table = 'payroll_taxes';

    protected $fillable = [
        'company_id',
        'payroll_id',
        'payroll_entry_id',
        'employee_id',
        'tax_type',
        'name',
        'rate',
        'amount',
        'taxable_amount',
        'is_employer_tax',
        'description',
        'status',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'is_employer_tax' => 'boolean',
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

    public function calculateAmount(float $taxableAmount): float
    {
        if ($this->amount > 0) {
            return $this->amount;
        }

        if ($this->rate > 0) {
            return $taxableAmount * ($this->rate / 100);
        }

        return 0;
    }

    public function scopeEmployeeTax($query)
    {
        return $query->where('is_employer_tax', false);
    }

    public function scopeEmployerTax($query)
    {
        return $query->where('is_employer_tax', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('tax_type', $type);
    }
}