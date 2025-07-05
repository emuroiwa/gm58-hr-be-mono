<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LeaveRequest;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\LeaveTypeResource;
use App\Services\LeaveService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeaveController extends Controller
{
    public function __construct(
        private LeaveService $leaveService
    ) {}

    /**
     * Get leave requests
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');
        $filters = $request->only(['employee_id', 'status', 'leave_type_id', 'date_from', 'date_to', 'per_page']);

        // If user is employee, only show their leaves
        if ($request->user()->role === 'employee') {
            $filters['employee_id'] = $request->user()->employee->id;
        }

        $leaves = $this->leaveService->getCompanyLeaves($companyId, $filters);

        return response()->json([
            'data' => LeaveResource::collection($leaves),
            'meta' => [
                'current_page' => $leaves->currentPage(),
                'last_page' => $leaves->lastPage(),
                'per_page' => $leaves->perPage(),
                'total' => $leaves->total(),
            ]
        ]);
    }

    /**
     * Apply for leave
     */
    public function store(LeaveRequest $request): JsonResponse
    {
        try {
            $employeeId = $request->user()->employee->id;
            $data = $request->validated();

            $leave = $this->leaveService->applyLeave($employeeId, $data);

            return response()->json([
                'message' => 'Leave application submitted successfully',
                'data' => new LeaveResource($leave)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit leave application',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get specific leave request
     */
    public function show(Request $request, string $leaveId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            // Implementation would use leave service to get specific leave
            
            return response()->json([
                'data' => new LeaveResource($leave->load(['employee', 'leaveType', 'approvedBy', 'rejectedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve leave request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve leave request
     */
    public function approve(Request $request, string $leaveId): JsonResponse
    {
        $request->validate([
            'comments' => 'nullable|string|max:1000'
        ]);

        try {
            $companyId = $request->get('company_id');
            $approverId = $request->user()->employee->id;
            $comments = $request->get('comments');

            $leave = $this->leaveService->approveLeave($leaveId, $companyId, $approverId, $comments);

            return response()->json([
                'message' => 'Leave request approved successfully',
                'data' => new LeaveResource($leave->load(['employee', 'leaveType', 'approvedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve leave request',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Reject leave request
     */
    public function reject(Request $request, string $leaveId): JsonResponse
    {
        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);

        try {
            $companyId = $request->get('company_id');
            $rejectedBy = $request->user()->employee->id;
            $comments = $request->get('comments');

            $leave = $this->leaveService->rejectLeave($leaveId, $companyId, $rejectedBy, $comments);

            return response()->json([
                'message' => 'Leave request rejected',
                'data' => new LeaveResource($leave->load(['employee', 'leaveType', 'rejectedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reject leave request',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get leave balance for employee
     */
    public function getLeaveBalance(Request $request): JsonResponse
    {
        try {
            $employeeId = $request->user()->employee->id;
            $balance = $this->leaveService->getLeaveBalance($employeeId);

            return response()->json([
                'data' => $balance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve leave balance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get leave types
     */
    public function getLeaveTypes(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $leaveTypes = \App\Models\LeaveType::where('company_id', $companyId)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'data' => LeaveTypeResource::collection($leaveTypes)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve leave types',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
