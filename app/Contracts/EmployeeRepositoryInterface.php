<?php
// app/Contracts/EmployeeRepositoryInterface.php

namespace App\Contracts;

use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface
{
    // Basic CRUD
    public function find(string $id): ?Employee;
    public function create(array $data): Employee;
    public function update(string $id, array $data): Employee;
    public function delete(string $id): bool;
    
    // Company-specific methods
    public function findByCompany(string $companyId, array $filters = []): LengthAwarePaginator;
    public function findByIdAndCompany(string $id, string $companyId): ?Employee;
    public function getByCompany(string $companyId, array $filters = []): LengthAwarePaginator;
    public function getByCompanyWithFilters(string $companyId, array $filters = []): LengthAwarePaginator;
    
    // Other methods
    public function findByUserId(string $userId): ?Employee;
    public function getByDepartment(string $departmentId): Collection;
    public function generateEmployeeNumber(string $companyId): string;
    public function generateEmployeeId(string $companyId): string;
    
    // Report methods
    public function countByCompany(string $companyId): int;
    public function countActiveByCompany(string $companyId): int;
    public function countInactiveByCompany(string $companyId): int;
    public function getEmployeesByDepartment(string $companyId): Collection;
    public function getRecentHires(string $companyId, int $days = 30): Collection;
    public function getUpcomingBirthdays(string $companyId, int $days = 7): Collection;
}