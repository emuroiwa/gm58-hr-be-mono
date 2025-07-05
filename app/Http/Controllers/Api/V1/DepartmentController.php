<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\EmployeeResource;
use App\Services\CompanyService;
use App\Events\DepartmentCreated;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function __construct(
        private CompanyService $companyService
    ) {}

    /**
     * Get all departments for company
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $departments = $this->companyService->getDepartments($companyId);

            return response()->json([
                'data' => DepartmentResource::collection($departments),
                'meta' => [
                    'total' => $departments->count(),
                    'active' => $departments->where('is_active', true)->count(),
                    'inactive' => $departments->where('is_active', false)->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve departments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific department
     */
    public function show(Request $request, string $departmentId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $department = \App\Models\Department::where('id', $departmentId)
                ->where('company_id', $companyId)
                ->with(['manager', 'employees'])
                ->first();

            if (!$department) {
                return response()->json([
                    'message' => 'Department not found'
                ], 404);
            }

            return response()->json([
                'data' => new DepartmentResource($department)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new department
     */
    public function store(DepartmentRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $department = $this->companyService->createDepartment($companyId, $data);

            // Fire department created event
            event(new DepartmentCreated($department->load('manager')));

            return response()->json([
                'message' => 'Department created successfully',
                'data' => new DepartmentResource($department)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create department',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update department
     */
    public function update(DepartmentRequest $request, string $departmentId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $department = $this->companyService->updateDepartment($departmentId, $companyId, $data);

            return response()->json([
                'message' => 'Department updated successfully',
                'data' => new DepartmentResource($department->load('manager'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update department',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete department
     */
    public function destroy(Request $request, string $departmentId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            
            // Check if department has employees
            $employeeCount = \App\Models\Employee::where('department_id', $departmentId)
                ->where('status', 'active')
                ->count();

            if ($employeeCount > 0) {
                return response()->json([
                    'message' => 'Cannot delete department with active employees',
                    'active_employees' => $employeeCount
                ], 422);
            }

            $result = $this->companyService->deleteDepartment($departmentId, $companyId);

            return response()->json([
                'message' => 'Department deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete department',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get employees in department
     */
    public function getEmployees(Request $request, string $departmentId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = array_merge(
                $request->only(['status', 'search', 'per_page']),
                ['department_id' => $departmentId]
            );

            $employees = \App\Models\Employee::where('company_id', $companyId)
                ->where('department_id', $departmentId)
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
                ->with(['user', 'position', 'manager'])
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
                'message' => 'Failed to retrieve department employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
