<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBenefit extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'benefit_id',
        'enrollment_date',
        'effective_date',
        'end_date',
        'employee_contribution',
        'employer_contribution',
        'total_contribution',
        'coverage_amount',
        'beneficiary_name',
        'beneficiary_relationship',
        'beneficiary_percentage',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'effective_date' => 'date',
        'end_date' => 'date',
        'employee_contribution' => 'decimal:2',
        'employer_contribution' => 'decimal:2',
        'total_contribution' => 'decimal:2',
        'coverage_amount' => 'decimal:2',
        'beneficiary_percentage' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>', now());
    }

    public function scopeByBenefitType($query, string $type)
    {
        return $query->whereHas('benefit', function ($q) use ($type) {
            $q->where('type', $type);
        });
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

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->end_date && 
               $this->end_date <= now()->addDays($days) && 
               $this->end_date > now();
    }

    public function calculateAnnualCost(): float
    {
        if ($this->benefit->contribution_frequency === 'monthly') {
            return $this->total_contribution * 12;
        } elseif ($this->benefit->contribution_frequency === 'bi-weekly') {
            return $this->total_contribution * 26;
        } elseif ($this->benefit->contribution_frequency === 'weekly') {
            return $this->total_contribution * 52;
        }
        
        return $this->total_contribution;
    }

    public function calculateEmployeeAnnualCost(): float
    {
        if ($this->benefit->contribution_frequency === 'monthly') {
            return $this->employee_contribution * 12;
        } elseif ($this->benefit->contribution_frequency === 'bi-weekly') {
            return $this->employee_contribution * 26;
        } elseif ($this->benefit->contribution_frequency === 'weekly') {
            return $this->employee_contribution * 52;
        }
        
        return $this->employee_contribution;
    }

    public function calculateEmployerAnnualCost(): float
    {
        if ($this->benefit->contribution_frequency === 'monthly') {
            return $this->employer_contribution * 12;
        } elseif ($this->benefit->contribution_frequency === 'bi-weekly') {
            return $this->employer_contribution * 26;
        } elseif ($this->benefit->contribution_frequency === 'weekly') {
            return $this->employer_contribution * 52;
        }
        
        return $this->employer_contribution;
    }

    public function getDaysUntilExpirationAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }
        
        return now()->diffInDays($this->end_date, false);
    }

    public function getContributionFrequencyAttribute(): string
    {
        return $this->benefit->contribution_frequency ?? 'monthly';
    }
}