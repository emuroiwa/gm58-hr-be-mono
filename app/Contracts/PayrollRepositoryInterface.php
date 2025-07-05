<?php

namespace App\Contracts;

use App\Models\PayrollPeriod;
use App\Models\PayrollEntry;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PayrollRepositoryInterface
{
    public function getAllPeriods(int $companyId, array $filters = []): LengthAwarePaginator;
    public function createPeriod(array $data): PayrollPeriod;
    public function findPeriod(int $id, int $companyId): ?PayrollPeriod;
    public function updatePeriod(int $id, array $data, int $companyId): bool;
    public function deletePeriod(int $id, int $companyId): bool;
    public function getActivePayrollPeriod(int $companyId): ?PayrollPeriod;
    public function processPayroll(int $periodId, int $companyId): Collection;
    public function getPayrollEntries(int $periodId, int $companyId): Collection;
    public function createPayrollEntry(array $data): PayrollEntry;
    public function calculateTotalPayroll(int $periodId, int $companyId): float;
}
