<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'type',
        'category',
        'duration',
        'max_participants',
        'instructor',
        'location',
        'start_date',
        'end_date',
        'cost',
        'status',
        'materials',
        'requirements',
        'objectives',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'duration' => 'decimal:2',
        'cost' => 'decimal:2',
        'max_participants' => 'integer',
        'materials' => 'array',
        'requirements' => 'array',
        'objectives' => 'array',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'training_employee')
                    ->withPivot([
                        'enrolled_at',
                        'completed_at',
                        'status',
                        'score',
                        'certificate_path',
                        'notes'
                    ])
                    ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeInProgress($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getAvailableSlotsAttribute(): int
    {
        $enrolled = $this->employees()->wherePivot('status', '!=', 'cancelled')->count();
        return max(0, $this->max_participants - $enrolled);
    }

    public function getEnrollmentCountAttribute(): int
    {
        return $this->employees()->wherePivot('status', '!=', 'cancelled')->count();
    }

    public function getCompletionRateAttribute(): float
    {
        $enrolled = $this->getEnrollmentCountAttribute();
        if ($enrolled === 0) {
            return 0;
        }
        
        $completed = $this->employees()->wherePivot('status', 'completed')->count();
        return ($completed / $enrolled) * 100;
    }

    public function isUpcoming(): bool
    {
        return $this->start_date > now();
    }

    public function isInProgress(): bool
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }

    public function isCompleted(): bool
    {
        return $this->end_date < now() || $this->status === 'completed';
    }

    public function canEnroll(): bool
    {
        return $this->status === 'active' && 
               $this->getAvailableSlotsAttribute() > 0 && 
               $this->start_date > now();
    }
}