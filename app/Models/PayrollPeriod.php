<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'start_date',
        'end_date',
        'pay_date',
        'status',
        'total_gross_pay',
        'total_net_pay',
        'total_deductions',
        'total_taxes',
        'employee_count',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pay_date' => 'date',
        'total_gross_pay' => 'decimal:2',
        'total_net_pay' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'employee_count' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function payrollEntries(): HasMany
    {
        return $this->hasMany(PayrollEntry::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function canBeProcessed(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }
}
