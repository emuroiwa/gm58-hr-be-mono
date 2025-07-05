<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'pay_date' => $this->pay_date?->format('Y-m-d'),
            'status' => $this->status,
            'description' => $this->description,
            
            // Financial Summary
            'total_gross' => $this->total_gross,
            'total_deductions' => $this->total_deductions,
            'total_net' => $this->total_net,
            
            // Employee Information
            'total_employees' => $this->whenCounted('payrolls'),
            'processed_employees' => $this->payrolls()->where('status', 'processed')->count(),
            
            // Processing Information
            'processed_at' => $this->processed_at?->toISOString(),
            'processed_by' => new UserResource($this->whenLoaded('processedBy')),
            'error_message' => $this->error_message,
            
            // Actions
            'can_process' => $this->status === 'draft',
            'can_export' => $this->status === 'processed',
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
