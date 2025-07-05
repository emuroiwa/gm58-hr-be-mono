<?php

namespace App\Repositories;

use App\Contracts\DepartmentRepositoryInterface;
use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function getAllDepartments(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Department::where('company_id', $companyId);
        
        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->withCount('employees')
                    ->orderBy('name')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createDepartment(array $data): Department
    {
        return Department::create($data);
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
                        ->update($data);
    }

    public function deleteDepartment(int $id, int $companyId): bool
    {
        return Department::where('id', $id)
                        ->where('company_id', $companyId)
                        ->delete();
    }

    public function getDepartmentEmployees(int $departmentId, int $companyId): Collection
    {
        return Department::where('id', $departmentId)
                        ->where('company_id', $companyId)
                        ->first()
                        ->employees()
                        ->with(['position', 'user'])
                        ->get();
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
                        ->with(['parent', 'children'])
                        ->orderBy('name')
                        ->get();
    }
}
