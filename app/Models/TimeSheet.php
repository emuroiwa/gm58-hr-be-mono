<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimeSheet extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'total_hours',
        'overtime_hours',
        'status',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculateTotalHours(): float
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $totalMinutes = $this->clock_out->diffInMinutes($this->clock_in);
        
        if ($this->break_start && $this->break_end) {
            $breakMinutes = $this->break_end->diffInMinutes($this->break_start);
            $totalMinutes -= $breakMinutes;
        }

        return round($totalMinutes / 60, 2);
    }

    public function calculateOvertimeHours(float $regularHours = 8): float
    {
        $totalHours = $this->calculateTotalHours();
        return max(0, $totalHours - $regularHours);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('date', $year)
                    ->whereMonth('date', $month);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['submitted', 'pending']);
    }
}