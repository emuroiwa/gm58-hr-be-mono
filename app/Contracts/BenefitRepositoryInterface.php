<?php

namespace App\Contracts;

use App\Models\Benefit;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BenefitRepositoryInterface
{
    public function getAllBenefits(int $companyId, array $filters = []): LengthAwarePaginator;
    public function createBenefit(array $data): Benefit;
    public function findBenefit(int $id, int $companyId): ?Benefit;
    public function updateBenefit(int $id, array $data, int $companyId): bool;
    public function deleteBenefit(int $id, int $companyId): bool;
    public function getEmployeeBenefits(int $employeeId): Collection;
    public function assignBenefitToEmployee(int $benefitId, int $employeeId, array $data = []): bool;
    public function removeBenefitFromEmployee(int $benefitId, int $employeeId): bool;
    public function calculateBenefitAmount(int $benefitId, int $employeeId): float;
}
