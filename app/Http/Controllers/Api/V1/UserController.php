<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get all users for company
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['role', 'is_active', 'search', 'per_page']);

            $users = $this->userRepository->getByCompanyWithFilters($companyId, $filters);

            return response()->json([
                'data' => UserResource::collection($users),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new user
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();
            $data['company_id'] = $companyId;
            $data['password'] = Hash::make($data['password']);

            $user = $this->userRepository->create($data);

            return response()->json([
                'message' => 'User created successfully',
                'data' => new UserResource($user->load(['employee', 'company']))
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get specific user
     */
    public function show(Request $request, string $userId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $user = $this->userRepository->findByIdAndCompany($userId, $companyId);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json([
                'data' => new UserResource($user->load(['employee', 'company']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function update(UserRequest $request, string $userId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $data = $request->validated();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user = $this->userRepository->updateByIdAndCompany($userId, $companyId, $data);

            return response()->json([
                'message' => 'User updated successfully',
                'data' => new UserResource($user->load(['employee', 'company']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, string $userId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $this->userRepository->deleteByIdAndCompany($userId, $companyId);

            return response()->json([
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Activate user
     */
    public function activate(Request $request, string $userId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $user = $this->userRepository->updateByIdAndCompany($userId, $companyId, ['is_active' => true]);

            return response()->json([
                'message' => 'User activated successfully',
                'data' => new UserResource($user)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to activate user',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Deactivate user
     */
    public function deactivate(Request $request, string $userId): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $user = $this->userRepository->updateByIdAndCompany($userId, $companyId, ['is_active' => false]);

            return response()->json([
                'message' => 'User deactivated successfully',
                'data' => new UserResource($user)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to deactivate user',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, string $userId): JsonResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            $companyId = $request->get('company_id');
            $password = Hash::make($request->get('password'));
            
            $user = $this->userRepository->updateByIdAndCompany($userId, $companyId, [
                'password' => $password
            ]);

            return response()->json([
                'message' => 'Password reset successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
