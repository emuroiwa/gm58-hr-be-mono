<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Contracts\EmployeeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function findByCompany(string $companyId, array $filters = []): LengthAwarePaginator
    {
        return QueryBuilder::for(Employee::class)
            ->where('company_id', $companyId)
            ->allowedFilters(['department_id', 'position_id', 'employment_status', 'is_active'])
            ->allowedSorts(['first_name', 'last_name', 'hire_date', 'created_at'])
            ->allowedIncludes(['department', 'position', 'currency', 'manager'])
            ->paginate(request('per_page', 15));
    }

    public function findByIdAndCompany(string $id, string $companyId): ?Employee
    {
        return Employee::where('id', $id)
            ->where('company_id', $companyId)
            ->with(['department', 'position', 'currency', 'manager'])
            ->first();
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(string $id, array $data): Employee
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee->fresh(['department', 'position', 'currency']);
    }

    public function delete(string $id): bool
    {
        return Employee::findOrFail($id)->delete();
    }

    public function getByDepartment(string $departmentId): Collection
    {
        return Employee::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get();
    }

    public function generateEmployeeNumber(string $companyId): string
    {
        $count = Employee::where('company_id', $companyId)->count();
        return 'EMP' . str_pad($count + 1, 6, '0', STR_PAD_LEFT);
    }
}
