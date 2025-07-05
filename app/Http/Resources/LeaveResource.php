<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'leave_type' => new LeaveTypeResource($this->whenLoaded('leaveType')),
            
            // Leave Details
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'days' => $this->days,
            'reason' => $this->reason,
            'emergency_contact' => $this->emergency_contact,
            'emergency_phone' => $this->emergency_phone,
            
            // Status Information
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            
            // Approval Information
            'applied_at' => $this->applied_at?->toISOString(),
            'approved_by' => new EmployeeResource($this->whenLoaded('approvedBy')),
            'approved_at' => $this->approved_at?->toISOString(),
            'rejected_by' => new EmployeeResource($this->whenLoaded('rejectedBy')),
            'rejected_at' => $this->rejected_at?->toISOString(),
            'comments' => $this->comments,
            
            // Attachments
            'attachments' => $this->attachments ? 
                array_map(fn($file) => url('storage/' . $file), $this->attachments) : [],
            
            // Actions
            'can_edit' => $this->status === 'pending',
            'can_cancel' => in_array($this->status, ['pending', 'approved']),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    private function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'orange',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }
}
