<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\PositionResource;
use App\Services\CompanyService;
use App\Services\FileUploadService;
use App\Events\CompanyUpdated;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $companyService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Get company information
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $company = $this->companyService->getCompany($companyId);

            if (!$company) {
                return response()->json([
                    'message' => 'Company not found'
                ], 404);
            }

            return response()->json([
                'data' => new CompanyResource($company->load('currency'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve company information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company information
     */
    public function update(CompanyUpdateRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $this->fileUploadService->uploadCompanyLogo(
                    $request->file('logo'), 
                    $companyId
                );
                $data['logo'] = $logoPath;
            }

            $originalCompany = $this->companyService->getCompany($companyId);
            $company = $this->companyService->updateCompany($companyId, $data);

            // Fire company updated event
            $changes = array_diff_assoc($data, $originalCompany->toArray());
            event(new CompanyUpdated($company, $changes));

            return response()->json([
                'message' => 'Company information updated successfully',
                'data' => new CompanyResource($company->load('currency'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update company information',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Upload company logo
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);

        try {
            $companyId = $request->get('company_id');
            
            $logoPath = $this->fileUploadService->uploadCompanyLogo(
                $request->file('logo'), 
                $companyId
            );

            $company = $this->companyService->updateCompany($companyId, ['logo' => $logoPath]);

            return response()->json([
                'message' => 'Company logo uploaded successfully',
                'data' => [
                    'logo_url' => url('storage/' . $logoPath),
                    'logo_path' => $logoPath
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload company logo',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get company settings
     */
    public function getSettings(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $company = $this->companyService->getCompany($companyId);

            if (!$company) {
                return response()->json(['message' => 'Company not found'], 404);
            }

            return response()->json([
                'data' => [
                    'company_info' => [
                        'name' => $company->name,
                        'email' => $company->email,
                        'phone' => $company->phone,
                        'website' => $company->website,
                        'timezone' => $company->timezone,
                        'currency_id' => $company->currency_id,
                    ],
                    'business_settings' => [
                        'tax_id' => $company->tax_id,
                        'registration_number' => $company->registration_number,
                        'employee_limit' => $company->employee_limit,
                    ],
                    'system_settings' => $company->settings ?? [],
                    'subscription' => [
                        'status' => $company->subscription_status ?? 'active',
                        'employee_limit' => $company->employee_limit,
                        'features' => $this->getAvailableFeatures($company),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve company settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update company settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.working_hours' => 'sometimes|array',
            'settings.leave_policies' => 'sometimes|array',
            'settings.payroll_settings' => 'sometimes|array',
            'settings.notification_preferences' => 'sometimes|array',
        ]);

        try {
            $companyId = $request->get('company_id');
            $settings = $request->get('settings');

            $company = $this->companyService->updateCompany($companyId, [
                'settings' => $settings
            ]);

            return response()->json([
                'message' => 'Company settings updated successfully',
                'data' => $company->settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update company settings',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get company statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $stats = $this->companyService->getCompanyStats($companyId);

            return response()->json([
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve company statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company organization structure
     */
    public function getOrganizationStructure(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            
            $departments = $this->companyService->getDepartments($companyId);
            $positions = $this->companyService->getPositions($companyId);

            $structure = [
                'departments' => DepartmentResource::collection($departments),
                'positions' => PositionResource::collection($positions),
                'organizational_chart' => $this->buildOrganizationalChart($companyId),
            ];

            return response()->json([
                'data' => $structure
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve organization structure',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available features based on subscription
     */
    private function getAvailableFeatures($company): array
    {
        // This would typically be based on subscription plan
        return [
            'employee_management' => true,
            'payroll_processing' => true,
            'attendance_tracking' => true,
            'leave_management' => true,
            'performance_reviews' => $company->employee_limit > 10,
            'advanced_reporting' => $company->employee_limit > 25,
            'api_access' => true,
            'custom_fields' => $company->employee_limit > 50,
            'integrations' => $company->employee_limit > 100,
        ];
    }

    /**
     * Build organizational chart
     */
    private function buildOrganizationalChart($companyId): array
    {
        $employees = \App\Models\Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->with(['department', 'position', 'manager'])
            ->get();

        // Build hierarchical structure
        $chart = [];
        $topLevel = $employees->whereNull('manager_id');

        foreach ($topLevel as $employee) {
            $chart[] = $this->buildEmployeeNode($employee, $employees);
        }

        return $chart;
    }

    /**
     * Build employee node for org chart
     */
    private function buildEmployeeNode($employee, $allEmployees): array
    {
        $subordinates = $allEmployees->where('manager_id', $employee->id);
        
        $node = [
            'id' => $employee->id,
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'title' => $employee->job_title,
            'department' => $employee->department?->name,
            'position' => $employee->position?->title,
            'avatar' => $employee->avatar ? url('storage/' . $employee->avatar) : null,
            'subordinates' => []
        ];

        foreach ($subordinates as $subordinate) {
            $node['subordinates'][] = $this->buildEmployeeNode($subordinate, $allEmployees);
        }

        return $node;
    }
}
