<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'trainer' => $this->trainer,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'duration_hours' => $this->duration_hours,
            'location' => $this->location,
            'cost' => $this->cost,
            'max_participants' => $this->max_participants,
            'current_participants' => $this->whenCounted('participants'),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'participants' => EmployeeResource::collection($this->whenLoaded('participants')),
            'can_enroll' => $this->canEnroll(),
            'is_full' => $this->isFull(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'scheduled' => 'Scheduled',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    private function getStatusColor(): string
    {
        return match($this->status) {
            'scheduled' => 'blue',
            'ongoing' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    private function canEnroll(): bool
    {
        return $this->status === 'scheduled' && !$this->isFull();
    }

    private function isFull(): bool
    {
        if (!$this->max_participants) return false;
        return $this->participants()->count() >= $this->max_participants;
    }
}
