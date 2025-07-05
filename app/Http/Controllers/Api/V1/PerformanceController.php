<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PerformanceRequest;
use App\Http\Resources\PerformanceResource;
use App\Services\PerformanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PerformanceController extends Controller
{
    public function __construct(
        private PerformanceService $performanceService
    ) {}

    /**
     * Get performance reviews
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['employee_id', 'reviewer_id', 'status', 'year', 'per_page']);

            $reviews = $this->performanceService->getCompanyReviews($companyId, $filters);

            return response()->json([
                'data' => PerformanceResource::collection($reviews),
                'meta' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve performance reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create performance review
     */
    public function store(PerformanceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $review = $this->performanceService->createReview($data['employee_id'], $data);

            return response()->json([
                'message' => 'Performance review created successfully',
                'data' => new PerformanceResource($review->load(['employee', 'reviewer']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create performance review',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get specific performance review
     */
    public function show(Request $request, string $reviewId): JsonResponse
    {
        try {
            $review = \App\Models\Performance::with(['employee', 'reviewer', 'approvedBy'])
                ->findOrFail($reviewId);

            return response()->json([
                'data' => new PerformanceResource($review)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve performance review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update performance review
     */
    public function update(PerformanceRequest $request, string $reviewId): JsonResponse
    {
        try {
            $employeeId = $request->user()->employee->id;
            $data = $request->validated();

            $review = $this->performanceService->updateReview($reviewId, $employeeId, $data);

            return response()->json([
                'message' => 'Performance review updated successfully',
                'data' => new PerformanceResource($review->load(['employee', 'reviewer']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update performance review',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete performance review
     */
    public function destroy(Request $request, string $reviewId): JsonResponse
    {
        try {
            $review = \App\Models\Performance::findOrFail($reviewId);
            $review->delete();

            return response()->json([
                'message' => 'Performance review deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete performance review',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Submit performance review
     */
    public function submit(Request $request, string $reviewId): JsonResponse
    {
        try {
            $employeeId = $request->user()->employee->id;
            $review = $this->performanceService->submitReview($reviewId, $employeeId);

            return response()->json([
                'message' => 'Performance review submitted successfully',
                'data' => new PerformanceResource($review->load(['employee', 'reviewer']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit performance review',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Approve performance review
     */
    public function approve(Request $request, string $reviewId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $approverId = $request->user()->employee->id;

            $review = $this->performanceService->approveReview($reviewId, $companyId, $approverId);

            return response()->json([
                'message' => 'Performance review approved successfully',
                'data' => new PerformanceResource($review->load(['employee', 'reviewer', 'approvedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve performance review',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get employee performance reviews
     */
    public function getEmployeeReviews(Request $request, string $employeeId): JsonResponse
    {
        try {
            $filters = $request->only(['year', 'status', 'per_page']);
            $reviews = $this->performanceService->getEmployeeReviews($employeeId, $filters);

            return response()->json([
                'data' => PerformanceResource::collection($reviews),
                'meta' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve employee performance reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
