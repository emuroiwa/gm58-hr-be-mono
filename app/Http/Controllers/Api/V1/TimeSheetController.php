<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TimeSheetRequest;
use App\Http\Resources\TimeSheetResource;
use App\Services\TimeSheetService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TimeSheetController extends Controller
{
    public function __construct(
        private TimeSheetService $timeSheetService
    ) {}

    /**
     * Get time sheets
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['employee_id', 'status', 'project', 'date_from', 'date_to', 'per_page']);

            // If user is employee, only show their timesheets
            if ($request->user()->role === 'employee') {
                $filters['employee_id'] = $request->user()->employee->id;
            }

            $timeSheets = $this->timeSheetService->getCompanyTimeSheets($companyId, $filters);

            return response()->json([
                'data' => TimeSheetResource::collection($timeSheets),
                'meta' => [
                    'current_page' => $timeSheets->currentPage(),
                    'last_page' => $timeSheets->lastPage(),
                    'per_page' => $timeSheets->perPage(),
                    'total' => $timeSheets->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve time sheets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create time entry
     */
    public function store(TimeSheetRequest $request): JsonResponse
    {
        try {
            $employeeId = $request->user()->employee->id;
            $data = $request->validated();

            $timeSheet = $this->timeSheetService->createTimeEntry($employeeId, $data);

            return response()->json([
                'message' => 'Time entry created successfully',
                'data' => new TimeSheetResource($timeSheet->load(['employee']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create time entry',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get specific time sheet
     */
    public function show(Request $request, string $timeSheetId): JsonResponse
    {
        try {
            $timeSheet = \App\Models\TimeSheet::with(['employee', 'approvedBy'])
                ->findOrFail($timeSheetId);

            return response()->json([
                'data' => new TimeSheetResource($timeSheet)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve time sheet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update time entry
     */
    public function update(TimeSheetRequest $request, string $timeSheetId): JsonResponse
    {
        try {
            $employeeId = $request->user()->employee->id;
            $data = $request->validated();

            $timeSheet = $this->timeSheetService->updateTimeEntry($timeSheetId, $employeeId, $data);

            return response()->json([
                'message' => 'Time entry updated successfully',
                'data' => new TimeSheetResource($timeSheet->load(['employee']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update time entry',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete time entry
     */
    public function destroy(Request $request, string $timeSheetId): JsonResponse
    {
        try {
            $timeSheet = \App\Models\TimeSheet::findOrFail($timeSheetId);
            
            // Check if user can delete this timesheet
            if ($request->user()->employee->id !== $timeSheet->employee_id && 
                !in_array($request->user()->role, ['admin', 'hr'])) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $timeSheet->delete();

            return response()->json([
                'message' => 'Time entry deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete time entry',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Submit timesheet for approval
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'week_start_date' => 'required|date'
        ]);

        try {
            $employeeId = $request->user()->employee->id;
            $weekStartDate = $request->get('week_start_date');

            $result = $this->timeSheetService->submitTimeSheet($employeeId, $weekStartDate);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to submit timesheet',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Approve timesheet
     */
    public function approve(Request $request, string $timeSheetId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $approverId = $request->user()->employee->id;

            $timeSheet = $this->timeSheetService->approveTimeSheet($timeSheetId, $companyId, $approverId);

            return response()->json([
                'message' => 'Timesheet approved successfully',
                'data' => new TimeSheetResource($timeSheet->load(['employee', 'approvedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve timesheet',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Reject timesheet
     */
    public function reject(Request $request, string $timeSheetId): JsonResponse
    {
        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);

        try {
            $timeSheet = \App\Models\TimeSheet::findOrFail($timeSheetId);
            
            $timeSheet->update([
                'status' => 'rejected',
                'approved_by' => $request->user()->employee->id,
                'approved_at' => now(),
                'comments' => $request->get('comments'),
            ]);

            return response()->json([
                'message' => 'Timesheet rejected',
                'data' => new TimeSheetResource($timeSheet->load(['employee', 'approvedBy']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reject timesheet',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get employee timesheets
     */
    public function getEmployeeTimeSheets(Request $request, string $employeeId): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'status', 'project', 'per_page']);
            $timeSheets = $this->timeSheetService->getEmployeeTimeSheets($employeeId, $filters);

            return response()->json([
                'data' => TimeSheetResource::collection($timeSheets),
                'meta' => [
                    'current_page' => $timeSheets->currentPage(),
                    'last_page' => $timeSheets->lastPage(),
                    'per_page' => $timeSheets->perPage(),
                    'total' => $timeSheets->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve employee timesheets',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
