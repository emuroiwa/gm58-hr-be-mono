<?php

namespace App\Services;

use App\Contracts\EmployeeRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\DepartmentRepositoryInterface;
use App\Contracts\PositionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Events\EmployeeCreated;
use App\Events\EmployeeUpdated;
use App\Events\EmployeeDeleted;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepository,
        private UserRepositoryInterface $userRepository,
        private DepartmentRepositoryInterface $departmentRepository,
        private PositionRepositoryInterface $positionRepository
    ) {}

    public function getAllEmployees($companyId, array $filters = [])
    {
        return $this->employeeRepository->getByCompanyWithFilters($companyId, $filters);
    }

    public function getEmployee($id, $companyId)
    {
        return $this->employeeRepository->findByIdAndCompany($id, $companyId);
    }

    public function createEmployee($companyId, array $data)
    {
        return DB::transaction(function () use ($companyId, $data) {
            // Generate employee ID
            $employeeId = $this->generateEmployeeId($companyId);

            // Create user account if email provided
            $user = null;
            if (isset($data['email']) && isset($data['create_user_account']) && $data['create_user_account']) {
                $user = $this->userRepository->create([
                    'company_id' => $companyId,
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'] ?? 'password123'),
                    'role' => $data['role'] ?? 'employee',
                    'is_active' => true,
                ]);
            }

            // Create employee
            $employee = $this->employeeRepository->create([
                'company_id' => $companyId,
                'user_id' => $user?->id,
                'employee_id' => $employeeId,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'hire_date' => $data['hire_date'] ?? now(),
                'department_id' => $data['department_id'] ?? null,
                'position_id' => $data['position_id'] ?? null,
                'manager_id' => $data['manager_id'] ?? null,
                'salary' => $data['salary'] ?? null,
                'currency_id' => $data['currency_id'] ?? 1,
                'job_title' => $data['job_title'] ?? null,
                'employment_type' => $data['employment_type'] ?? 'full_time',
                'status' => $data['status'] ?? 'active',
                'avatar' => $data['avatar'] ?? null,
            ]);

            if ($user) {
                $this->userRepository->update($user->id, ['employee_id' => $employee->id]);
            }

            event(new EmployeeCreated($employee));

            return $this->employeeRepository->findWithRelations($employee->id, ['user', 'department', 'position', 'manager']);
        });
    }

    public function updateEmployee($id, $companyId, array $data)
    {
        return DB::transaction(function () use ($id, $companyId, $data) {
            $employee = $this->employeeRepository->findByIdAndCompany($id, $companyId);
            
            if (!$employee) {
                throw new \Exception('Employee not found');
            }

            $updatedEmployee = $this->employeeRepository->update($id, [
                'first_name' => $data['first_name'] ?? $employee->first_name,
                'last_name' => $data['last_name'] ?? $employee->last_name,
                'email' => $data['email'] ?? $employee->email,
                'phone' => $data['phone'] ?? $employee->phone,
                'date_of_birth' => $data['date_of_birth'] ?? $employee->date_of_birth,
                'gender' => $data['gender'] ?? $employee->gender,
                'address' => $data['address'] ?? $employee->address,
                'city' => $data['city'] ?? $employee->city,
                'state' => $data['state'] ?? $employee->state,
                'country' => $data['country'] ?? $employee->country,
                'postal_code' => $data['postal_code'] ?? $employee->postal_code,
                'department_id' => $data['department_id'] ?? $employee->department_id,
                'position_id' => $data['position_id'] ?? $employee->position_id,
                'manager_id' => $data['manager_id'] ?? $employee->manager_id,
                'salary' => $data['salary'] ?? $employee->salary,
                'job_title' => $data['job_title'] ?? $employee->job_title,
                'employment_type' => $data['employment_type'] ?? $employee->employment_type,
                'status' => $data['status'] ?? $employee->status,
            ]);

            // Update user email if changed
            if ($employee->user && isset($data['email']) && $employee->email !== $data['email']) {
                $this->userRepository->update($employee->user->id, ['email' => $data['email']]);
            }

            event(new EmployeeUpdated($updatedEmployee));

            return $this->employeeRepository->findWithRelations($id, ['user', 'department', 'position', 'manager']);
        });
    }

    public function deleteEmployee($id, $companyId)
    {
        return DB::transaction(function () use ($id, $companyId) {
            $employee = $this->employeeRepository->findByIdAndCompany($id, $companyId);
            
            if (!$employee) {
                throw new \Exception('Employee not found');
            }

            // Soft delete user account
            if ($employee->user) {
                $this->userRepository->delete($employee->user->id);
            }

            // Soft delete employee
            $this->employeeRepository->delete($id);

            event(new EmployeeDeleted($employee));

            return ['message' => 'Employee deleted successfully'];
        });
    }

    private function generateEmployeeId($companyId)
    {
        $lastEmployee = $this->employeeRepository->getLastEmployeeByCompany($companyId);

        if (!$lastEmployee) {
            return 'EMP001';
        }

        $lastId = (int) substr($lastEmployee->employee_id, 3);
        return 'EMP' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
    }
}
