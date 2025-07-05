<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TimeSheetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'break_duration' => $this->break_duration,
            'duration' => $this->duration,
            'duration_formatted' => $this->getDurationFormatted(),
            'description' => $this->description,
            'project' => $this->project,
            'task' => $this->task,
            'billable' => $this->billable,
            'hourly_rate' => $this->hourly_rate,
            'total_amount' => $this->getTotalAmount(),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'approved_by' => new EmployeeResource($this->whenLoaded('approvedBy')),
            'approved_at' => $this->approved_at?->toISOString(),
            'can_edit' => $this->canEdit(),
            'can_approve' => $this->canApprove(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function getDurationFormatted(): string
    {
        if (!$this->duration) return '0h 0m';
        
        $hours = intval($this->duration / 60);
        $minutes = $this->duration % 60;
        
        return "{$hours}h {$minutes}m";
    }

    private function getTotalAmount(): float
    {
        if (!$this->billable || !$this->hourly_rate || !$this->duration) return 0;
        
        $hours = $this->duration / 60;
        return round($hours * $this->hourly_rate, 2);
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    private function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    private function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    private function canApprove(): bool
    {
        return $this->status === 'submitted';
    }
}
