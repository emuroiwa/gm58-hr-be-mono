<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TrainingRequest;
use App\Http\Resources\TrainingResource;
use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrainingController extends Controller
{
    /**
     * Get training programs
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['status', 'start_date', 'end_date', 'trainer', 'per_page']);

            $query = \App\Models\Training::where('company_id', $companyId)
                ->with(['participants']);

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['start_date'])) {
                $query->where('start_date', '>=', $filters['start_date']);
            }

            if (isset($filters['end_date'])) {
                $query->where('end_date', '<=', $filters['end_date']);
            }

            if (isset($filters['trainer'])) {
                $query->where('trainer', 'LIKE', "%{$filters['trainer']}%");
            }

            $trainings = $query->orderBy('start_date', 'desc')
                ->paginate($filters['per_page'] ?? 15);

            return response()->json([
                'data' => TrainingResource::collection($trainings),
                'meta' => [
                    'current_page' => $trainings->currentPage(),
                    'last_page' => $trainings->lastPage(),
                    'per_page' => $trainings->perPage(),
                    'total' => $trainings->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve training programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create training program
     */
    public function store(TrainingRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();
            $data['company_id'] = $companyId;

            $training = \App\Models\Training::create($data);

            return response()->json([
                'message' => 'Training program created successfully',
                'data' => new TrainingResource($training)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create training program',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get specific training program
     */
    public function show(Request $request, string $trainingId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $training = \App\Models\Training::where('id', $trainingId)
                ->where('company_id', $companyId)
                ->with(['participants'])
                ->first();

            if (!$training) {
                return response()->json(['message' => 'Training program not found'], 404);
            }

            return response()->json([
                'data' => new TrainingResource($training)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve training program',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update training program
     */
    public function update(TrainingRequest $request, string $trainingId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            $training = \App\Models\Training::where('id', $trainingId)
                ->where('company_id', $companyId)
                ->first();

            if (!$training) {
                return response()->json(['message' => 'Training program not found'], 404);
            }

            $training->update($data);

            return response()->json([
                'message' => 'Training program updated successfully',
                'data' => new TrainingResource($training)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update training program',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete training program
     */
    public function destroy(Request $request, string $trainingId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $training = \App\Models\Training::where('id', $trainingId)
                ->where('company_id', $companyId)
                ->first();

            if (!$training) {
                return response()->json(['message' => 'Training program not found'], 404);
            }

            $training->delete();

            return response()->json([
                'message' => 'Training program deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete training program',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Enroll employee in training
     */
    public function enroll(Request $request, string $trainingId): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        try {
            $companyId = $request->get('company_id');
            $employeeId = $request->get('employee_id');

            $training = \App\Models\Training::where('id', $trainingId)
                ->where('company_id', $companyId)
                ->first();

            if (!$training) {
                return response()->json(['message' => 'Training program not found'], 404);
            }

            // Check if already enrolled
            if ($training->participants()->where('employee_id', $employeeId)->exists()) {
                return response()->json(['message' => 'Employee already enrolled in this training'], 422);
            }

            // Check capacity
            if ($training->max_participants && $training->participants()->count() >= $training->max_participants) {
                return response()->json(['message' => 'Training program is full'], 422);
            }

            $training->participants()->attach($employeeId, [
                'status' => 'enrolled',
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Employee enrolled in training successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to enroll employee in training',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Mark training as complete for employee
     */
    public function markComplete(Request $request, string $trainingId): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'score' => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:1000'
        ]);

        try {
            $employeeId = $request->get('employee_id');
            $score = $request->get('score');
            $feedback = $request->get('feedback');

            $training = \App\Models\Training::findOrFail($trainingId);

            $training->participants()->updateExistingPivot($employeeId, [
                'status' => 'completed',
                'completed_at' => now(),
                'score' => $score,
                'feedback' => $feedback,
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Training marked as complete for employee'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark training as complete',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get employee trainings
     */
    public function getEmployeeTrainings(Request $request, string $employeeId): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'year', 'per_page']);

            $query = \App\Models\Training::whereHas('participants', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })->with(['participants' => function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            }]);

            if (isset($filters['status'])) {
                $query->whereHas('participants', function ($q) use ($employeeId, $filters) {
                    $q->where('employee_id', $employeeId)
                      ->where('status', $filters['status']);
                });
            }

            if (isset($filters['year'])) {
                $query->whereYear('start_date', $filters['year']);
            }

            $trainings = $query->orderBy('start_date', 'desc')
                ->paginate($filters['per_page'] ?? 15);

            return response()->json([
                'data' => TrainingResource::collection($trainings),
                'meta' => [
                    'current_page' => $trainings->currentPage(),
                    'last_page' => $trainings->lastPage(),
                    'per_page' => $trainings->perPage(),
                    'total' => $trainings->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve employee trainings',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
