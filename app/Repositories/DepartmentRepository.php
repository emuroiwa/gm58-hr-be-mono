<?php
// app/Repositories/DepartmentRepository.php

namespace App\Repositories;

use App\Models\Department;
use App\Contracts\DepartmentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    // Basic CRUD methods
    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function find(string $id): ?Department
    {
        return Department::with(['manager', 'employees'])->find($id);
    }

    public function update(string $id, array $data): Department
    {
        $department = Department::findOrFail($id);
        $department->update($data);
        return $department->fresh(['manager']);
    }

    public function delete(string $id): bool
    {
        return Department::destroy($id) > 0;
    }

    // Company-specific methods
    public function getByCompany(string $companyId): Collection
    {
        return Department::where('company_id', $companyId)
            ->with('manager')
            ->orderBy('name')
            ->get();
    }

    public function findByIdAndCompany(string $departmentId, string $companyId): ?Department
    {
        return Department::where('id', $departmentId)
            ->where('company_id', $companyId)
            ->with(['manager', 'employees'])
            ->first();
    }

    // Legacy methods for compatibility
    public function getAllDepartments(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Department::where('company_id', $companyId)
            ->with('manager');

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createDepartment(array $data): Department
    {
        return $this->create($data);
    }

    public function findDepartment(int $id, int $companyId): ?Department
    {
        return Department::where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    }

    public function updateDepartment(int $id, array $data, int $companyId): bool
    {
        return Department::where('id', $id)
            ->where('company_id', $companyId)
            ->update($data) > 0;
    }

    public function deleteDepartment(int $id, int $companyId): bool
    {
        return Department::where('id', $id)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }

    public function getDepartmentEmployees(int $departmentId, int $companyId): Collection
    {
        $department = $this->findDepartment($departmentId, $companyId);
        
        if (!$department) {
            return collect();
        }

        return $department->employees;
    }

    public function getDepartmentsByManager(int $managerId, int $companyId): Collection
    {
        return Department::where('company_id', $companyId)
            ->where('manager_id', $managerId)
            ->get();
    }

    public function getDepartmentHierarchy(int $companyId): Collection
    {
        return Department::where('company_id', $companyId)
            ->whereNull('parent_id')
            ->with('children')
            ->get();
    }
}