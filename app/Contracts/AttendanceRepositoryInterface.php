<?php

namespace App\Contracts;

use App\Models\Attendance;
use App\Models\TimeEntry;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

interface AttendanceRepositoryInterface
{
    public function getAttendanceByEmployee(int $employeeId, Carbon $startDate, Carbon $endDate): Collection;
    public function recordAttendance(array $data): Attendance;
    public function updateAttendance(int $id, array $data): bool;
    public function deleteAttendance(int $id): bool;
    public function getAttendanceByCompany(int $companyId, array $filters = []): LengthAwarePaginator;
    public function createTimeEntry(array $data): TimeEntry;
    public function getTimeEntries(int $employeeId, Carbon $date): Collection;
    public function calculateHoursWorked(int $employeeId, Carbon $startDate, Carbon $endDate): float;
    public function getAttendanceReport(int $companyId, Carbon $startDate, Carbon $endDate): Collection;
}
