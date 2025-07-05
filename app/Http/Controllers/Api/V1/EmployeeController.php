<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateEmployeeRequest;
use App\Http\Requests\Api\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeCollection;
use App\Services\EmployeeService;
use App\Services\FileUploadService;
use App\Jobs\ImportEmployees;
use App\Jobs\GenerateReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeService $employeeService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Get paginated list of employees
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');
        $filters = $request->only(['status', 'department_id', 'search', 'per_page']);

        $employees = $this->employeeService->getAllEmployees($companyId, $filters);

        return response()->json([
            'data' => new EmployeeCollection($employees),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ]
        ]);
    }

    /**
     * Get specific employee details
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $employee = $this->employeeService->getEmployee($id, $companyId);

            if (!$employee) {
                return response()->json([
                    'message' => 'Employee not found'
                ], 404);
            }

            return response()->json([
                'data' => new EmployeeResource($employee->load(['user', 'department', 'position', 'manager']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new employee
     */
    public function store(CreateEmployeeRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $employee = $this->employeeService->createEmployee($companyId, $data);

            return response()->json([
                'message' => 'Employee created successfully',
                'data' => new EmployeeResource($employee)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create employee',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update employee information
     */
    public function update(UpdateEmployeeRequest $request, string $id): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $employee = $this->employeeService->updateEmployee($id, $companyId, $data);

            return response()->json([
                'message' => 'Employee updated successfully',
                'data' => new EmployeeResource($employee)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update employee',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete employee
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $result = $this->employeeService->deleteEmployee($id, $companyId);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete employee',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Upload employee avatar
     */
    public function uploadAvatar(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $companyId = $request->get('company_id');
            $employee = $this->employeeService->getEmployee($id, $companyId);

            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $avatarPath = $this->fileUploadService->uploadAvatar($request->file('avatar'), $id);
            
            $updatedEmployee = $this->employeeService->updateEmployee($id, $companyId, ['avatar' => $avatarPath]);

            return response()->json([
                'message' => 'Avatar uploaded successfully',
                'data' => new EmployeeResource($updatedEmployee)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload avatar',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Import employees from file
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,json|max:10240', // 10MB max
            'options' => 'sometimes|array'
        ]);

        try {
            $companyId = $request->get('company_id');
            $file = $request->file('file');
            $filePath = $file->store('imports', 'local');
            $options = $request->get('options', []);

            // Dispatch import job
            ImportEmployees::dispatch($companyId, $request->user()->id, $filePath, $options);

            return response()->json([
                'message' => 'Employee import started. You will be notified when complete.',
                'import_status' => 'processing'
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to start import',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Export employees to file
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'sometimes|in:csv,excel,pdf',
            'filters' => 'sometimes|array'
        ]);

        try {
            $companyId = $request->get('company_id');
            $format = $request->get('format', 'csv');
            $filters = $request->get('filters', []);

            // Dispatch export job
            GenerateReport::dispatch($companyId, $request->user()->id, 'employee', $filters, $format);

            return response()->json([
                'message' => 'Employee export started. You will be notified when complete.',
                'export_status' => 'processing'
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to start export',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
