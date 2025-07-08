<?php
// app/Contracts/PayrollRepositoryInterface.php

namespace App\Contracts;

use App\Models\PayrollPeriod;
use App\Models\Payroll;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PayrollRepositoryInterface
{
    // Period methods
    public function createPeriod(array $data): PayrollPeriod;
    public function findPeriod(string $id): ?PayrollPeriod;
    public function findPeriodByIdAndCompany(string $periodId, string $companyId): ?PayrollPeriod;
    public function getPeriodsWithFilters(string $companyId, array $filters = []): LengthAwarePaginator;
    
    // Payroll methods
    public function create(array $data): Payroll;
    public function getByPeriod(string $periodId): Collection;
    public function getPayrollDetailsByPeriod(string $periodId): array;
    public function getPayrollSummaryByCompany(string $companyId): array;
}