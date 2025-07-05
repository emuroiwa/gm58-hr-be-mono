<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PositionRequest;
use App\Http\Resources\PositionResource;
use App\Http\Resources\EmployeeResource;
use App\Services\CompanyService;
use App\Events\PositionCreated;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    public function __construct(
        private CompanyService $companyService
    ) {}

    /**
     * Get all positions for company
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $departmentId = $request->get('department_id');
            
            $positions = $this->companyService->getPositions($companyId, $departmentId);

            return response()->json([
                'data' => PositionResource::collection($positions),
                'meta' => [
                    'total' => $positions->count(),
                    'active' => $positions->where('is_active', true)->count(),
                    'inactive' => $positions->where('is_active', false)->count(),
                    'by_department' => $positions->groupBy('department.name')->map->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve positions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific position
     */
    public function show(Request $request, string $positionId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $position = \App\Models\Position::where('id', $positionId)
                ->where('company_id', $companyId)
                ->with(['department', 'employees'])
                ->first();

            if (!$position) {
                return response()->json([
                    'message' => 'Position not found'
                ], 404);
            }

            return response()->json([
                'data' => new PositionResource($position)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve position',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new position
     */
    public function store(PositionRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $position = $this->companyService->createPosition($companyId, $data);

            // Fire position created event
            event(new PositionCreated($position->load('department')));

            return response()->json([
                'message' => 'Position created successfully',
                'data' => new PositionResource($position)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create position',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update position
     */
    public function update(PositionRequest $request, string $positionId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $position = $this->companyService->updatePosition($positionId, $companyId, $data);

            return response()->json([
                'message' => 'Position updated successfully',
                'data' => new PositionResource($position->load('department'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update position',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete position
     */
    public function destroy(Request $request, string $positionId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            
            // Check if position has employees
            $employeeCount = \App\Models\Employee::where('position_id', $positionId)
                ->where('status', 'active')
                ->count();

            if ($employeeCount > 0) {
                return response()->json([
                    'message' => 'Cannot delete position with active employees',
                    'active_employees' => $employeeCount
                ], 422);
            }

            $result = $this->companyService->deletePosition($positionId, $companyId);

            return response()->json([
                'message' => 'Position deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete position',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get employees in position
     */
    public function getEmployees(Request $request, string $positionId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = array_merge(
                $request->only(['status', 'search', 'per_page']),
                ['position_id' => $positionId]
            );

            $employees = \App\Models\Employee::where('company_id', $companyId)
                ->where('position_id', $positionId)
                ->when($filters['status'] ?? null, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->when($filters['search'] ?? null, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                          ->orWhere('last_name', 'LIKE', "%{$search}%")
                          ->orWhere('job_title', 'LIKE', "%{$search}%");
                    });
                })
                ->with(['user', 'department', 'manager'])
                ->paginate($filters['per_page'] ?? 15);

            return response()->json([
                'data' => EmployeeResource::collection($employees),
                'meta' => [
                    'current_page' => $employees->currentPage(),
                    'last_page' => $employees->lastPage(),
                    'per_page' => $employees->perPage(),
                    'total' => $employees->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve position employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get salary range statistics for position
     */
    public function getSalaryStats(Request $request, string $positionId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            
            $position = \App\Models\Position::where('id', $positionId)
                ->where('company_id', $companyId)
                ->first();

            if (!$position) {
                return response()->json(['message' => 'Position not found'], 404);
            }

            $salaryStats = \App\Models\Employee::where('position_id', $positionId)
                ->where('status', 'active')
                ->whereNotNull('salary')
                ->selectRaw('
                    COUNT(*) as employee_count,
                    AVG(salary) as average_salary,
                    MIN(salary) as min_salary,
                    MAX(salary) as max_salary,
                    STDDEV(salary) as salary_deviation
                ')
                ->first();

            return response()->json([
                'data' => [
                    'position' => new PositionResource($position),
                    'salary_statistics' => [
                        'employee_count' => $salaryStats->employee_count ?? 0,
                        'average_salary' => round($salaryStats->average_salary ?? 0, 2),
                        'min_salary' => $salaryStats->min_salary ?? 0,
                        'max_salary' => $salaryStats->max_salary ?? 0,
                        'salary_deviation' => round($salaryStats->salary_deviation ?? 0, 2),
                        'defined_min_salary' => $position->min_salary,
                        'defined_max_salary' => $position->max_salary,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve salary statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
