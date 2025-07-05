<?php

namespace App\Contracts;

use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeRepositoryInterface
{
    public function findByCompany(string $companyId, array $filters = []): LengthAwarePaginator;
    public function findByIdAndCompany(string $id, string $companyId): ?Employee;
    public function create(array $data): Employee;
    public function update(string $id, array $data): Employee;
    public function delete(string $id): bool;
    public function getByDepartment(string $departmentId): Collection;
    public function generateEmployeeNumber(string $companyId): string;
}
