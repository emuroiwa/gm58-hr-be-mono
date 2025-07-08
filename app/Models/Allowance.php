<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allowance extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'name',
        'type',
        'amount',
        'percentage',
        'is_taxable',
        'is_recurring',
        'frequency',
        'effective_date',
        'end_date',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:4',
        'is_taxable' => 'boolean',
        'is_recurring' => 'boolean',
        'effective_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('effective_date', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeOneTime($query)
    {
        return $query->where('is_recurring', false);
    }

    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    public function scopeNonTaxable($query)
    {
        return $query->where('is_taxable', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->effective_date <= now() &&
               ($this->end_date === null || $this->end_date >= now());
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date < now();
    }

    public function calculateAmount(float $baseSalary): float
    {
        if ($this->amount > 0) {
            return $this->amount;
        }

        if ($this->percentage > 0) {
            return $baseSalary * ($this->percentage / 100);
        }

        return 0;
    }

    public function getMonthlyAmountAttribute(): float
    {
        if ($this->frequency === 'monthly') {
            return $this->amount;
        } elseif ($this->frequency === 'annual') {
            return $this->amount / 12;
        } elseif ($this->frequency === 'bi-weekly') {
            return $this->amount * 26 / 12;
        } elseif ($this->frequency === 'weekly') {
            return $this->amount * 52 / 12;
        }

        return $this->amount;
    }

    public function getAnnualAmountAttribute(): float
    {
        if ($this->frequency === 'annual') {
            return $this->amount;
        } elseif ($this->frequency === 'monthly') {
            return $this->amount * 12;
        } elseif ($this->frequency === 'bi-weekly') {
            return $this->amount * 26;
        } elseif ($this->frequency === 'weekly') {
            return $this->amount * 52;
        }

        return $this->amount;
    }
}