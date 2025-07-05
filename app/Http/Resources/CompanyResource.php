<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'logo' => $this->logo ? url('storage/' . $this->logo) : null,
            
            // Address Information
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            
            // Business Information
            'tax_id' => $this->tax_id,
            'registration_number' => $this->registration_number,
            'timezone' => $this->timezone,
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            
            // Status
            'is_active' => $this->is_active,
            'subscription_status' => $this->subscription_status,
            'employee_limit' => $this->employee_limit,
            
            // Settings
            'settings' => $this->settings,
            
            // Counts
            'total_employees' => $this->whenCounted('employees'),
            'total_departments' => $this->whenCounted('departments'),
            'total_positions' => $this->whenCounted('positions'),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
