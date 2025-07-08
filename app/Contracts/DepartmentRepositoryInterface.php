<?php
// app/Contracts/DepartmentRepositoryInterface.php

namespace App\Contracts;

use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DepartmentRepositoryInterface
{
    // Basic CRUD
    public function create(array $data): Department;
    public function find(string $id): ?Department;
    public function update(string $id, array $data): Department;
    public function delete(string $id): bool;
    
    // Company-specific methods
    public function getByCompany(string $companyId): Collection;
    public function findByIdAndCompany(string $departmentId, string $companyId): ?Department;
    
    // Legacy methods for compatibility
    public function getAllDepartments(int $companyId, array $filters = []): LengthAwarePaginator;
    public function createDepartment(array $data): Department;
    public function findDepartment(int $id, int $companyId): ?Department;
    public function updateDepartment(int $id, array $data, int $companyId): bool;
    public function deleteDepartment(int $id, int $companyId): bool;
    public function getDepartmentEmployees(int $departmentId, int $companyId): Collection;
    public function getDepartmentsByManager(int $managerId, int $companyId): Collection;
    public function getDepartmentHierarchy(int $companyId): Collection;
}