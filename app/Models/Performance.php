<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Performance extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'reviewer_id',
        'period_start',
        'period_end',
        'review_type',
        'overall_rating',
        'goals',
        'achievements',
        'strengths',
        'areas_for_improvement',
        'development_plan',
        'comments',
        'status',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'overall_rating' => 'decimal:2',
        'goals' => 'array',
        'achievements' => 'array',
        'strengths' => 'array',
        'areas_for_improvement' => 'array',
        'development_plan' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function scopeForPeriod($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate]);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('review_type', $type);
    }

    public function scopeByRating($query, float $minRating, float $maxRating = null)
    {
        $query->where('overall_rating', '>=', $minRating);
        
        if ($maxRating) {
            $query->where('overall_rating', '<=', $maxRating);
        }
        
        return $query;
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeReviewed(): bool
    {
        return in_array($this->status, ['submitted', 'in_progress']);
    }

    public function getRatingLevel(): string
    {
        if ($this->overall_rating >= 4.5) {
            return 'Excellent';
        } elseif ($this->overall_rating >= 3.5) {
            return 'Good';
        } elseif ($this->overall_rating >= 2.5) {
            return 'Satisfactory';
        } elseif ($this->overall_rating >= 1.5) {
            return 'Needs Improvement';
        } else {
            return 'Poor';
        }
    }
}