<?php
// app/Repositories/EmployeeRepository.php

namespace App\Repositories;

use App\Models\Employee;
use App\Contracts\EmployeeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    /**
     * Find employee by ID
     */
    public function find(string $id): ?Employee
    {
        return Employee::find($id);
    }

    /**
     * Create new employee
     */
    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    /**
     * Update employee
     */
    public function update(string $id, array $data): Employee
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee->fresh(['department', 'position', 'currency']);
    }

    /**
     * Delete employee
     */
    public function delete(string $id): bool
    {
        return Employee::destroy($id) > 0;
    }

    /**
     * Find employees by company with filters
     */
    public function findByCompany(string $companyId, array $filters = []): LengthAwarePaginator
    {
        return QueryBuilder::for(Employee::class)
            ->where('company_id', $companyId)
            ->allowedFilters(['department_id', 'position_id', 'employment_status', 'is_active'])
            ->allowedSorts(['first_name', 'last_name', 'hire_date', 'created_at'])
            ->allowedIncludes(['department', 'position', 'currency', 'manager', 'user'])
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find employee by ID and company
     */
    public function findByIdAndCompany(string $id, string $companyId): ?Employee
    {
        return Employee::where('id', $id)
            ->where('company_id', $companyId)
            ->with(['department', 'position', 'currency', 'manager', 'user'])
            ->first();
    }

    /**
     * Get employees by company (alias for findByCompany)
     */
    public function getByCompany(string $companyId, array $filters = []): LengthAwarePaginator
    {
        return $this->findByCompany($companyId, $filters);
    }

    /**
     * Get employees by company with filters
     */
    public function getByCompanyWithFilters(string $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Employee::where('company_id', $companyId)
            ->with(['department', 'position', 'manager', 'user']);

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('last_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('employee_id', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        if (isset($filters['employment_status'])) {
            $query->where('employment_status', $filters['employment_status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Find employee by user ID
     */
    public function findByUserId(string $userId): ?Employee
    {
        return Employee::where('user_id', $userId)->first();
    }

    /**
     * Get employees by department
     */
    public function getByDepartment(string $departmentId): Collection
    {
        return Employee::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Generate employee number
     */
    public function generateEmployeeNumber(string $companyId): string
    {
        $count = Employee::where('company_id', $companyId)->count();
        return 'EMP' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate employee ID (alias for generateEmployeeNumber)
     */
    public function generateEmployeeId(string $companyId): string
    {
        return $this->generateEmployeeNumber($companyId);
    }

    /**
     * Count total employees by company
     */
    public function countByCompany(string $companyId): int
    {
        return Employee::where('company_id', $companyId)->count();
    }

    /**
     * Count active employees by company
     */
    public function countActiveByCompany(string $companyId): int
    {
        return Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Count inactive employees by company
     */
    public function countInactiveByCompany(string $companyId): int
    {
        return Employee::where('company_id', $companyId)
            ->where('is_active', false)
            ->count();
    }

    /**
     * Get employee count by department
     */
    public function getEmployeesByDepartment(string $companyId): Collection
    {
        return Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->selectRaw('department_id, COUNT(*) as count')
            ->groupBy('department_id')
            ->with('department:id,name')
            ->get();
    }

    /**
     * Get recent hires
     */
    public function getRecentHires(string $companyId, int $days = 30): Collection
    {
        $date = Carbon::now()->subDays($days);
        
        return Employee::where('company_id', $companyId)
            ->where('hire_date', '>=', $date)
            ->where('is_active', true)
            ->with(['department', 'position'])
            ->orderBy('hire_date', 'desc')
            ->get();
    }

    /**
     * Get upcoming birthdays
     */
    public function getUpcomingBirthdays(string $companyId, int $days = 7): Collection
    {
        $today = Carbon::now();
        $endDate = Carbon::now()->addDays($days);
        
        return Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->whereNotNull('date_of_birth')
            ->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') BETWEEN ? AND ?", [
                $today->format('m-d'),
                $endDate->format('m-d')
            ])
            ->orderByRaw("DATE_FORMAT(date_of_birth, '%m-%d')")
            ->get();
    }
}