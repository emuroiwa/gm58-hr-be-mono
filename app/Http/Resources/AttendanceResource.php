<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'date' => $this->date,
            'check_in' => $this->check_in?->format('H:i:s'),
            'check_out' => $this->check_out?->format('H:i:s'),
            'break_duration' => $this->break_duration, // in minutes
            'worked_hours' => $this->worked_hours,
            'status' => $this->status,
            'notes' => $this->notes,
            'location' => $this->location,
            
            // Calculated Fields
            'is_late' => $this->isLate(),
            'is_early_departure' => $this->isEarlyDeparture(),
            'overtime_hours' => $this->getOvertimeHours(),
            
            // Status Indicators
            'status_color' => $this->getStatusColor(),
            'status_label' => $this->getStatusLabel(),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function isLate(): bool
    {
        if (!$this->check_in) return false;
        
        $standardStart = Carbon::parse('09:00:00');
        $checkIn = Carbon::parse($this->check_in);
        
        return $checkIn->gt($standardStart);
    }

    private function isEarlyDeparture(): bool
    {
        if (!$this->check_out) return false;
        
        $standardEnd = Carbon::parse('17:00:00');
        $checkOut = Carbon::parse($this->check_out);
        
        return $checkOut->lt($standardEnd);
    }

    private function getOvertimeHours(): float
    {
        if (!$this->worked_hours) return 0;
        
        $standardHours = 8;
        return max(0, $this->worked_hours - $standardHours);
    }

    private function getStatusColor(): string
    {
        return match($this->status) {
            'present' => 'green',
            'late' => 'orange',
            'absent' => 'red',
            'half_day' => 'blue',
            default => 'gray'
        };
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'present' => 'Present',
            'late' => 'Late',
            'absent' => 'Absent',
            'half_day' => 'Half Day',
            default => 'Unknown'
        };
    }
}
