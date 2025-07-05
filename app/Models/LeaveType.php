<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'days_per_year',
        'max_consecutive_days',
        'carry_forward_days',
        'requires_approval',
        'notice_period_days',
        'is_paid',
        'is_active',
        'color',
    ];

    protected $casts = [
        'days_per_year' => 'decimal:1',
        'max_consecutive_days' => 'integer',
        'carry_forward_days' => 'decimal:1',
        'notice_period_days' => 'integer',
        'requires_approval' => 'boolean',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function getUsedDaysForEmployee(Employee $employee, int $year = null): float
    {
        $year = $year ?? now()->year;
        
        return $this->leaves()
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('days_approved');
    }

    public function getRemainingDaysForEmployee(Employee $employee, int $year = null): float
    {
        $usedDays = $this->getUsedDaysForEmployee($employee, $year);
        return max(0, $this->days_per_year - $usedDays);
    }
}
