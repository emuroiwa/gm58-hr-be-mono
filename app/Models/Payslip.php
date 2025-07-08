<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'employee_id',
        'payroll_id',
        'payroll_period_id',
        'pay_date',
        'gross_pay',
        'net_pay',
        'total_deductions',
        'total_taxes',
        'ytd_gross',
        'ytd_net',
        'ytd_deductions',
        'ytd_taxes',
        'file_path',
        'status',
        'sent_at',
        'viewed_at',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'gross_pay' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'ytd_gross' => 'decimal:2',
        'ytd_net' => 'decimal:2',
        'ytd_deductions' => 'decimal:2',
        'ytd_taxes' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('pay_date', $year);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('pay_date', $year)
                    ->whereMonth('pay_date', $month);
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeViewed($query)
    {
        return $query->whereNotNull('viewed_at');
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isViewed(): bool
    {
        return $this->viewed_at !== null;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function markAsViewed(): void
    {
        $this->update(['viewed_at' => now()]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}