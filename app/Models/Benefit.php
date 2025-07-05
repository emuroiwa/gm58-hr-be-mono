<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Benefit extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'type',
        'value',
        'value_type',
        'eligibility_criteria',
        'effective_date',
        'end_date',
        'is_active',
        'is_taxable',
        'provider',
        'coverage_details',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'eligibility_criteria' => 'array',
        'coverage_details' => 'array',
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_taxable' => 'boolean',
    ];

    public function employeeBenefits(): HasMany
    {
        return $this->hasMany(EmployeeBenefit::class);
    }

    public function getEligibleEmployees()
    {
        // This would contain logic to determine eligible employees
        // based on eligibility_criteria
        return Employee::where('company_id', $this->company_id)
            ->where('status', 'active')
            ->get();
    }
}
