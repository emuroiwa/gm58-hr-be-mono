<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {}

    /**
     * Get attendance records
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');
        $filters = $request->only(['employee_id', 'date_from', 'date_to', 'status', 'per_page']);

        $attendances = $this->attendanceService->getAttendanceReport($companyId, $filters);

        return response()->json([
            'data' => AttendanceResource::collection($attendances),
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ]
        ]);
    }

    /**
     * Mark check-in
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $employeeId = $request->user()->employee->id;
            $data = [
                'date' => now()->format('Y-m-d'),
                'check_in' => now(),
                'status' => 'present',
                'location' => $request->get('location'),
                'notes' => $request->get('notes'),
            ];

            $attendance = $this->attendanceService->markAttendance($employeeId, $data);

            return response()->json([
                'message' => 'Check-in successful',
                'data' => new AttendanceResource($attendance->load('employee'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Check-in failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Mark check-out
     */
    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $employeeId = $request->user()->employee->id;
            $attendance = $this->attendanceService->markCheckOut($employeeId, now());

            return response()->json([
                'message' => 'Check-out successful',
                'data' => new AttendanceResource($attendance->load('employee'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Check-out failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get today's attendance for current user
     */
    public function getTodayAttendance(Request $request): JsonResponse
    {
        try {
            $employeeId = $request->user()->employee->id;
            $filters = ['date_from' => now()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')];
            
            $attendance = $this->attendanceService->getEmployeeAttendance($employeeId, $filters)->first();

            return response()->json([
                'data' => $attendance ? new AttendanceResource($attendance->load('employee')) : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve today\'s attendance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee attendance records
     */
    public function getEmployeeAttendance(Request $request, string $employeeId): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'status', 'per_page']);
            $attendances = $this->attendanceService->getEmployeeAttendance($employeeId, $filters);

            return response()->json([
                'data' => AttendanceResource::collection($attendances),
                'meta' => [
                    'current_page' => $attendances->currentPage(),
                    'last_page' => $attendances->lastPage(),
                    'per_page' => $attendances->perPage(),
                    'total' => $attendances->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve attendance records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create attendance record (admin/hr only)
     */
    public function store(AttendanceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $employeeId = $data['employee_id'] ?? $request->user()->employee->id;

            $attendance = $this->attendanceService->markAttendance($employeeId, $data);

            return response()->json([
                'message' => 'Attendance record created successfully',
                'data' => new AttendanceResource($attendance->load('employee'))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create attendance record',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update attendance record
     */
    public function update(AttendanceRequest $request, string $attendanceId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $attendance = $this->attendanceService->updateAttendance($attendanceId, $companyId, $data);

            return response()->json([
                'message' => 'Attendance record updated successfully',
                'data' => new AttendanceResource($attendance->load('employee'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update attendance record',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
