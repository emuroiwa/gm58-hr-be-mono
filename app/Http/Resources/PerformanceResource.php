<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerformanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'reviewer' => new EmployeeResource($this->whenLoaded('reviewer')),
            'review_period_start' => $this->review_period_start?->format('Y-m-d'),
            'review_period_end' => $this->review_period_end?->format('Y-m-d'),
            'goals' => $this->goals,
            'achievements' => $this->achievements,
            'areas_for_improvement' => $this->areas_for_improvement,
            'goals_next_period' => $this->goals_next_period,
            'comments' => $this->comments,
            'ratings' => [
                'overall_rating' => $this->overall_rating,
                'technical_skills' => $this->technical_skills,
                'communication_skills' => $this->communication_skills,
                'teamwork' => $this->teamwork,
                'leadership' => $this->leadership,
                'punctuality' => $this->punctuality,
                'average_rating' => $this->getAverageRating(),
            ],
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'submitted_at' => $this->submitted_at?->toISOString(),
            'approved_by' => new EmployeeResource($this->whenLoaded('approvedBy')),
            'approved_at' => $this->approved_at?->toISOString(),
            'can_edit' => $this->canEdit(),
            'can_submit' => $this->canSubmit(),
            'can_approve' => $this->canApprove(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function getAverageRating(): ?float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->technical_skills,
            $this->communication_skills,
            $this->teamwork,
            $this->leadership,
            $this->punctuality,
        ]);

        if (empty($ratings)) return null;

        return round(array_sum($ratings) / count($ratings), 2);
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            default => 'Unknown'
        };
    }

    private function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'submitted' => 'blue',
            'approved' => 'green',
            default => 'gray'
        };
    }

    private function canEdit(): bool
    {
        return $this->status === 'draft';
    }

    private function canSubmit(): bool
    {
        return $this->status === 'draft';
    }

    private function canApprove(): bool
    {
        return $this->status === 'submitted';
    }
}
