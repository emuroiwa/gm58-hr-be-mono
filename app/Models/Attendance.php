<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
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
        'hours_worked',
        'overtime_hours',
        'status',
        'location',
        'ip_address',
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
        'hours_worked' => 'decimal:2',
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

    public function calculateHoursWorked(): float
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $total = $this->clock_out->diffInMinutes($this->clock_in) / 60;
        
        if ($this->break_start && $this->break_end) {
            $breakTime = $this->break_end->diffInMinutes($this->break_start) / 60;
            $total -= $breakTime;
        }

        return round($total, 2);
    }

    public function isLate(): bool
    {
        // Assuming 9:00 AM is standard start time
        return $this->clock_in && $this->clock_in->format('H:i') > '09:00';
    }
}
