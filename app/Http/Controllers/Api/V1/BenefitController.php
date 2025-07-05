<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BenefitRequest;
use App\Http\Resources\BenefitResource;
use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BenefitController extends Controller
{
    /**
     * Get benefits
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['type', 'is_active', 'is_mandatory', 'per_page']);

            $query = \App\Models\Benefit::where('company_id', $companyId)
                ->withCount('employees');

            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (isset($filters['is_active'])) {
                $query->where('is_active', $filters['is_active']);
            }

            if (isset($filters['is_mandatory'])) {
                $query->where('is_mandatory', $filters['is_mandatory']);
            }

            $benefits = $query->orderBy('name')
                ->paginate($filters['per_page'] ?? 15);

            return response()->json([
                'data' => BenefitResource::collection($benefits),
                'meta' => [
                    'current_page' => $benefits->currentPage(),
                    'last_page' => $benefits->lastPage(),
                    'per_page' => $benefits->perPage(),
                    'total' => $benefits->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve benefits',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create benefit
     */
    public function store(BenefitRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();
            $data['company_id'] = $companyId;

            $benefit = \App\Models\Benefit::create($data);

            return response()->json([
                'message' => 'Benefit created successfully',
                'data' => new BenefitResource($benefit)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create benefit',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get specific benefit
     */
    public function show(Request $request, string $benefitId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $benefit = \App\Models\Benefit::where('id', $benefitId)
                ->where('company_id', $companyId)
                ->withCount('employees')
                ->first();

            if (!$benefit) {
                return response()->json(['message' => 'Benefit not found'], 404);
            }

            return response()->json([
                'data' => new BenefitResource($benefit)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve benefit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update benefit
     */
    public function update(BenefitRequest $request, string $benefitId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $benefit = \App\Models\Benefit::where('id', $benefitId)
                ->where('company_id', $companyId)
                ->first();

            if (!$benefit) {
                return response()->json(['message' => 'Benefit not found'], 404);
            }

            $benefit->update($data);

            return response()->json([
                'message' => 'Benefit updated successfully',
                'data' => new BenefitResource($benefit)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update benefit',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete benefit
     */
    public function destroy(Request $request, string $benefitId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $benefit = \App\Models\Benefit::where('id', $benefitId)
                ->where('company_id', $companyId)
                ->first();

            if (!$benefit) {
                return response()->json(['message' => 'Benefit not found'], 404);
            }

            // Check if benefit has enrolled employees
            if ($benefit->employees()->count() > 0) {
                return response()->json([
                    'message' => 'Cannot delete benefit with enrolled employees',
                    'enrolled_employees' => $benefit->employees()->count()
                ], 422);
            }

            $benefit->delete();

            return response()->json([
                'message' => 'Benefit deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete benefit',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Enroll employee in benefit
     */
    public function enroll(Request $request, string $benefitId): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'effective_date' => 'required|date',
            'employee_contribution' => 'nullable|numeric|min:0'
        ]);

        try {
            $companyId = $request->get('company_id');
            $employeeId = $request->get('employee_id');

            $benefit = \App\Models\Benefit::where('id', $benefitId)
                ->where('company_id', $companyId)
                ->first();

            if (!$benefit) {
                return response()->json(['message' => 'Benefit not found'], 404);
            }

            // Check if employee is already enrolled
            if ($benefit->employees()->where('employee_id', $employeeId)->exists()) {
                return response()->json(['message' => 'Employee already enrolled in this benefit'], 422);
            }

            $benefit->employees()->attach($employeeId, [
                'enrolled_at' => now(),
                'effective_date' => $request->get('effective_date'),
                'employee_contribution' => $request->get('employee_contribution', $benefit->employee_contribution),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Employee enrolled in benefit successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to enroll employee in benefit',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Unenroll employee from benefit
     */
    public function unenroll(Request $request, string $benefitId): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'end_date' => 'required|date'
        ]);

        try {
            $employeeId = $request->get('employee_id');
            $endDate = $request->get('end_date');

            $benefit = \App\Models\Benefit::findOrFail($benefitId);

            $benefit->employees()->updateExistingPivot($employeeId, [
                'end_date' => $endDate,
                'status' => 'inactive',
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Employee unenrolled from benefit successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to unenroll employee from benefit',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get employee benefits
     */
    public function getEmployeeBenefits(Request $request, string $employeeId): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'type', 'per_page']);

            $query = \App\Models\Benefit::whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })->with(['employees' => function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            }]);

            if (isset($filters['status'])) {
                $query->whereHas('employees', function ($q) use ($employeeId, $filters) {
                    $q->where('employee_id', $employeeId)
                      ->where('status', $filters['status']);
                });
            }

            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            $benefits = $query->orderBy('name')
                ->paginate($filters['per_page'] ?? 15);

            return response()->json([
                'data' => BenefitResource::collection($benefits),
                'meta' => [
                    'current_page' => $benefits->currentPage(),
                    'last_page' => $benefits->lastPage(),
                    'per_page' => $benefits->perPage(),
                    'total' => $benefits->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve employee benefits',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
