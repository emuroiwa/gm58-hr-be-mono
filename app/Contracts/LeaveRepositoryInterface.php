<?php
// app/Contracts/LeaveRepositoryInterface.php

namespace App\Contracts;

use App\Models\Leave;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LeaveRepositoryInterface
{
    public function create(array $data): Leave;
    public function find(string $id): ?Leave;
    public function update(string $id, array $data): Leave;
    public function getByEmployee(string $employeeId, array $filters = []): LengthAwarePaginator;
    public function getByCompany(string $companyId, array $filters = []): LengthAwarePaginator;
    public function countPendingLeaves(string $companyId): int;
    public function countOnLeaveToday(string $companyId): int;
    public function getLeaveReport(string $companyId, array $filters = []): array;
}