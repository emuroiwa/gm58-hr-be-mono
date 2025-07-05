<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'avatar' => $this->avatar ? url('storage/' . $this->avatar) : null,
            
            // Address Information
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            
            // Employment Information
            'hire_date' => $this->hire_date?->format('Y-m-d'),
            'job_title' => $this->job_title,
            'employment_type' => $this->employment_type,
            'status' => $this->status,
            'salary' => $this->when($this->canViewSalary($request), $this->salary),
            'years_of_service' => $this->hire_date ? now()->diffInYears($this->hire_date) : 0,
            
            // Relationships
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'position' => new PositionResource($this->whenLoaded('position')),
            'manager' => new EmployeeResource($this->whenLoaded('manager')),
            'user' => new UserResource($this->whenLoaded('user')),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function canViewSalary(Request $request): bool
    {
        $user = $request->user();
        
        // Admin and HR can view all salaries
        if (in_array($user->role, ['admin', 'hr'])) {
            return true;
        }
        
        // Employees can view their own salary
        if ($user->employee_id === $this->id) {
            return true;
        }
        
        // Managers can view their direct reports' salaries
        if ($user->role === 'manager' && $this->manager_id === $user->employee_id) {
            return true;
        }
        
        return false;
    }
}
