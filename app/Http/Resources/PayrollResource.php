<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'payroll_period' => new PayrollPeriodResource($this->whenLoaded('payrollPeriod')),
            
            // Earnings
            'basic_salary' => $this->basic_salary,
            'overtime_amount' => $this->overtime_amount,
            'bonus_amount' => $this->bonus_amount,
            'allowances' => $this->allowances,
            'gross_pay' => $this->gross_pay,
            
            // Deductions
            'tax_amount' => $this->tax_amount,
            'deductions' => $this->deductions,
            'net_pay' => $this->net_pay,
            
            // Work Information
            'working_days' => $this->working_days,
            'worked_days' => $this->worked_days,
            'attendance_percentage' => $this->working_days > 0 ? 
                round(($this->worked_days / $this->working_days) * 100, 2) : 0,
            
            // Status
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toISOString(),
            
            // Actions
            'can_download_slip' => true,
            'slip_url' => route('download.payslip', $this->id),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
