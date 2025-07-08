<?php
// app/Contracts/AttendanceRepositoryInterface.php

namespace App\Contracts;

use App\Models\Attendance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AttendanceRepositoryInterface
{
    public function create(array $data): Attendance;
    public function find(string $id): ?Attendance;
    public function update(string $id, array $data): Attendance;
    public function getTodayAttendance(string $employeeId): ?Attendance;
    public function getByEmployee(string $employeeId, array $filters = []): LengthAwarePaginator;
    public function getByCompany(string $companyId, array $filters = []): LengthAwarePaginator;
    public function countPresentToday(string $companyId): int;
    public function getReportData(string $companyId, string $startDate, string $endDate): Collection;
}