<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_requested' => 'decimal:1',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForPeriod($query, string $startDate, string $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isActive(): bool
    {
        return $this->isApproved() && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    public function isUpcoming(): bool
    {
        return $this->isApproved() && $this->start_date > now();
    }

    public function isPast(): bool
    {
        return $this->end_date < now();
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending' || 
               ($this->status === 'approved' && $this->start_date > now());
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getWorkingDaysAttribute(): int
    {
        $count = 0;
        $current = $this->start_date->copy();
        
        while ($current <= $this->end_date) {
            if ($current->isWeekday()) {
                $count++;
            }
            $current->addDay();
        }
        
        return $count;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }

    public function approve(User $approver, ?string $notes = null): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function reject(User $rejector, string $reason, ?string $notes = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'rejected_by' => $rejector->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'notes' => $notes,
        ]);
    }
}