<?php

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use App\Contracts\CompanyRepositoryInterface;
use App\Contracts\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CompanyRepositoryInterface $companyRepository,
        private EmployeeRepositoryInterface $employeeRepository
    ) {}

    public function login(array $credentials)
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        if (!$user->is_active) {
            throw new AuthenticationException('Account is inactive');
        }

        $this->userRepository->updateLastLogin($user->id);
        
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', 525600)
        ];
    }

    public function registerCompany(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create company
            $company = $this->companyRepository->create([
                'name' => $data['company_name'],
                'email' => $data['company_email'],
                'phone' => $data['company_phone'] ?? null,
                'address' => $data['company_address'] ?? null,
                'city' => $data['company_city'] ?? null,
                'state' => $data['company_state'] ?? null,
                'country' => $data['company_country'] ?? 'US',
                'currency_id' => $data['currency_id'] ?? 1,
                'timezone' => $data['timezone'] ?? 'UTC',
                'is_active' => true,
            ]);

            // Create admin user
            $user = $this->userRepository->create([
                'company_id' => $company->id,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Create employee record for admin
            $employee = $this->employeeRepository->create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'employee_id' => 'EMP001',
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'hire_date' => now(),
                'status' => 'active',
                'job_title' => 'Administrator',
            ]);

            $this->userRepository->update($user->id, ['employee_id' => $employee->id]);

            return [
                'company' => $company,
                'user' => $this->userRepository->findWithRelations($user->id, ['employee', 'company']),
                'message' => 'Company registered successfully'
            ];
        });
    }

    public function logout($user)
    {
        $user->currentAccessToken()->delete();
        return ['message' => 'Logged out successfully'];
    }

    public function refreshToken($user)
    {
        $user->currentAccessToken()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', 525600)
        ];
    }
}
