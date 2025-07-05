<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterCompanyRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyResource;
use App\Services\AuthService;
use App\Events\CompanyRegistered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Authenticate user and return token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            $result = $this->authService->login($credentials);

            return response()->json([
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($result['user']->load(['employee', 'company'])),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                    'expires_in' => $result['expires_in'],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Register new company with admin user
     */
    public function registerCompany(RegisterCompanyRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->authService->registerCompany($data);

            // Fire company registration event
            event(new CompanyRegistered($result['company'], $result['user']));

            return response()->json([
                'message' => 'Company registered successfully',
                'data' => [
                    'company' => new CompanyResource($result['company']),
                    'user' => new UserResource($result['user']),
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get authenticated user information
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['employee', 'company']);
        
        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $user = $request->user();
            $employee = $user->employee;

            if ($employee) {
                $employee->update($request->only(['first_name', 'last_name', 'phone']));
                
                if ($request->hasFile('avatar')) {
                    $avatarPath = $request->file('avatar')->store('avatars', 'public');
                    $employee->update(['avatar' => $avatarPath]);
                }
            }

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user->load(['employee', 'company']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Profile update failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Password change failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 422);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)])
            : response()->json(['message' => __($status)], 422);
    }

    /**
     * Refresh authentication token
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refreshToken($request->user());

            return response()->json([
                'message' => 'Token refreshed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Logout user and invalidate token
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->logout($request->user());

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
