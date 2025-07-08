<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'date',
        'type',
        'is_recurring',
        'recurrence_pattern',
        'is_mandatory',
        'is_paid',
        'applies_to_all',
        'departments',
        'positions',
        'created_by',
        'status',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_paid' => 'boolean',
        'applies_to_all' => 'boolean',
        'departments' => 'array',
        'positions' => 'array',
        'recurrence_pattern' => 'array',
        'metadata' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>', now());
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('date', now()->year);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('date', now()->year)
                    ->whereMonth('date', now()->month);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeForDepartment($query, string $departmentId)
    {
        return $query->where('applies_to_all', true)
                    ->orWhereJsonContains('departments', $departmentId);
    }

    public function scopeForPosition($query, string $positionId)
    {
        return $query->where('applies_to_all', true)
                    ->orWhereJsonContains('positions', $positionId);
    }

    public function scopeInDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    public function isUpcoming(): bool
    {
        return $this->date->isFuture();
    }

    public function isPast(): bool
    {
        return $this->date->isPast();
    }

    public function isThisWeek(): bool
    {
        return $this->date->isCurrentWeek();
    }

    public function isThisMonth(): bool
    {
        return $this->date->isCurrentMonth();
    }

    public function isThisYear(): bool
    {
        return $this->date->isCurrentYear();
    }

    public function getDaysUntilAttribute(): int
    {
        return now()->diffInDays($this->date, false);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('F j, Y');
    }

    public function getShortDateAttribute(): string
    {
        return $this->date->format('M j');
    }

    public function appliesTo(Employee $employee): bool
    {
        if ($this->applies_to_all) {
            return true;
        }

        if ($this->departments && in_array($employee->department_id, $this->departments)) {
            return true;
        }

        if ($this->positions && in_array($employee->position_id, $this->positions)) {
            return true;
        }

        return false;
    }

    public function getNextOccurrence(): ?Holiday
    {
        if (!$this->is_recurring || !$this->recurrence_pattern) {
            return null;
        }

        // Basic implementation for annual recurrence
        if ($this->recurrence_pattern['type'] === 'annual') {
            $nextDate = $this->date->addYear();
            
            return new self([
                'company_id' => $this->company_id,
                'name' => $this->name,
                'description' => $this->description,
                'date' => $nextDate,
                'type' => $this->type,
                'is_recurring' => $this->is_recurring,
                'recurrence_pattern' => $this->recurrence_pattern,
                'is_mandatory' => $this->is_mandatory,
                'is_paid' => $this->is_paid,
                'applies_to_all' => $this->applies_to_all,
                'departments' => $this->departments,
                'positions' => $this->positions,
                'created_by' => $this->created_by,
                'status' => $this->status,
            ]);
        }

        return null;
    }
}