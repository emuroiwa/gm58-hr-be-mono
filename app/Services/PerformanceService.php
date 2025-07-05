<?php

namespace App\Services;

use App\Contracts\PerformanceRepositoryInterface;

class PerformanceService
{
    public function __construct(
        private PerformanceRepositoryInterface $performanceRepository
    ) {}

    public function createReview($employeeId, array $data)
    {
        return $this->performanceRepository->create([
            'employee_id' => $employeeId,
            'reviewer_id' => $data['reviewer_id'],
            'review_period_start' => $data['review_period_start'],
            'review_period_end' => $data['review_period_end'],
            'goals' => $data['goals'] ?? null,
            'achievements' => $data['achievements'] ?? null,
            'areas_for_improvement' => $data['areas_for_improvement'] ?? null,
            'overall_rating' => $data['overall_rating'] ?? null,
            'comments' => $data['comments'] ?? null,
            'status' => 'draft',
        ]);
    }

    public function updateReview($reviewId, $employeeId, array $data)
    {
        $review = $this->performanceRepository->findByIdAndEmployee($reviewId, $employeeId);
        
        if (!$review) {
            throw new \Exception('Performance review not found');
        }

        return $this->performanceRepository->update($reviewId, $data);
    }

    public function submitReview($reviewId, $employeeId)
    {
        $review = $this->performanceRepository->findByIdAndEmployee($reviewId, $employeeId);
        
        if (!$review) {
            throw new \Exception('Performance review not found');
        }

        return $this->performanceRepository->update($reviewId, [
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function approveReview($reviewId, $companyId, $approverId)
    {
        $review = $this->performanceRepository->findByIdAndCompany($reviewId, $companyId);
        
        if (!$review) {
            throw new \Exception('Performance review not found');
        }

        return $this->performanceRepository->update($reviewId, [
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);
    }

    public function getEmployeeReviews($employeeId, array $filters = [])
    {
        return $this->performanceRepository->getEmployeeReviews($employeeId, $filters);
    }

    public function getCompanyReviews($companyId, array $filters = [])
    {
        return $this->performanceRepository->getCompanyReviews($companyId, $filters);
    }
}
