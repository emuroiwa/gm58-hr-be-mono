<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BenefitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'type_label' => $this->getTypeLabel(),
            'company_contribution' => $this->company_contribution,
            'employee_contribution' => $this->employee_contribution,
            'is_mandatory' => $this->is_mandatory,
            'is_active' => $this->is_active,
            'enrolled_employees' => $this->whenCounted('employees'),
            'enrollment_rate' => $this->getEnrollmentRate(),
            'total_cost' => $this->getTotalCost(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function getTypeLabel(): string
    {
        return match($this->type) {
            'health' => 'Health Insurance',
            'dental' => 'Dental Insurance',
            'vision' => 'Vision Insurance',
            'retirement' => 'Retirement Plan',
            'life_insurance' => 'Life Insurance',
            'other' => 'Other',
            default => 'Unknown'
        };
    }

    private function getEnrollmentRate(): float
    {
        $totalEmployees = $this->company->employees()->where('status', 'active')->count();
        if ($totalEmployees === 0) return 0;
        
        $enrolledEmployees = $this->employees()->count();
        return round(($enrolledEmployees / $totalEmployees) * 100, 2);
    }

    private function getTotalCost(): float
    {
        $enrolledEmployees = $this->employees()->count();
        return ($this->company_contribution + $this->employee_contribution) * $enrolledEmployees;
    }
}
