<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreatePayrollPeriodRequest;
use App\Http\Resources\PayrollPeriodResource;
use App\Http\Resources\PayrollResource;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PayrollController extends Controller
{
    public function __construct(
        private PayrollService $payrollService
    ) {}

    /**
     * Get payroll periods
     */
    public function getPeriods(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');
        $filters = $request->only(['year', 'status', 'per_page']);

        $periods = $this->payrollService->getPayrollPeriods($companyId, $filters);

        return response()->json([
            'data' => PayrollPeriodResource::collection($periods),
            'meta' => [
                'current_page' => $periods->currentPage(),
                'last_page' => $periods->lastPage(),
                'per_page' => $periods->perPage(),
                'total' => $periods->total(),
            ]
        ]);
    }

    /**
     * Create new payroll period
     */
    public function createPeriod(CreatePayrollPeriodRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $period = $this->payrollService->createPayrollPeriod($companyId, $data);

            return response()->json([
                'message' => 'Payroll period created successfully',
                'data' => new PayrollPeriodResource($period)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create payroll period',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get specific payroll period
     */
    public function showPeriod(Request $request, string $periodId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $period = $this->payrollService->getPayrollPeriods($companyId, ['id' => $periodId]);

            if (!$period) {
                return response()->json(['message' => 'Payroll period not found'], 404);
            }

            return response()->json([
                'data' => new PayrollPeriodResource($period)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payroll period',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payroll period
     */
    public function processPeriod(Request $request, string $periodId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $result = $this->payrollService->processPayrollPeriod($periodId, $companyId);

            return response()->json($result, 202);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to process payroll',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get payrolls for a period
     */
    public function getPeriodPayrolls(Request $request, string $periodId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = array_merge($request->only(['per_page']), ['payroll_period_id' => $periodId]);

            $payrolls = $this->payrollService->getPayrollsByPeriod($companyId, $filters);

            return response()->json([
                'data' => PayrollResource::collection($payrolls),
                'meta' => [
                    'current_page' => $payrolls->currentPage(),
                    'last_page' => $payrolls->lastPage(),
                    'per_page' => $payrolls->perPage(),
                    'total' => $payrolls->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payrolls',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee payrolls
     */
    public function getEmployeePayrolls(Request $request, string $employeeId): JsonResponse
    {
        try {
            $filters = $request->only(['year', 'per_page']);
            $payrolls = $this->payrollService->getEmployeePayrolls($employeeId, $filters);

            return response()->json([
                'data' => PayrollResource::collection($payrolls),
                'meta' => [
                    'current_page' => $payrolls->currentPage(),
                    'last_page' => $payrolls->lastPage(),
                    'per_page' => $payrolls->perPage(),
                    'total' => $payrolls->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve employee payrolls',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payroll details
     */
    public function show(Request $request, string $payrollId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $payroll = $this->payrollService->getPayrollDetails($payrollId, $companyId);

            if (!$payroll) {
                return response()->json(['message' => 'Payroll not found'], 404);
            }

            return response()->json([
                'data' => new PayrollResource($payroll->load(['employee', 'payrollPeriod']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve payroll',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pay slip for payroll
     */
    public function getPaySlip(Request $request, string $payrollId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $payroll = $this->payrollService->getPayrollDetails($payrollId, $companyId);

            if (!$payroll) {
                return response()->json(['message' => 'Payroll not found'], 404);
            }

            // Generate pay slip data
            $paySlipData = [
                'payroll' => new PayrollResource($payroll->load(['employee', 'payrollPeriod'])),
                'company' => $payroll->employee->company,
                'generated_at' => now()->toISOString(),
            ];

            return response()->json([
                'data' => $paySlipData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate pay slip',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
