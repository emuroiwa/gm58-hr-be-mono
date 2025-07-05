<?php

namespace App\Repositories;

use App\Contracts\BenefitRepositoryInterface;
use App\Models\Benefit;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BenefitRepository implements BenefitRepositoryInterface
{
    public function getAllBenefits(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Benefit::where('company_id', $companyId);
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->orderBy('name')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createBenefit(array $data): Benefit
    {
        return Benefit::create($data);
    }

    public function findBenefit(int $id, int $companyId): ?Benefit
    {
        return Benefit::where('id', $id)
                     ->where('company_id', $companyId)
                     ->first();
    }

    public function updateBenefit(int $id, array $data, int $companyId): bool
    {
        return Benefit::where('id', $id)
                     ->where('company_id', $companyId)
                     ->update($data);
    }

    public function deleteBenefit(int $id, int $companyId): bool
    {
        return Benefit::where('id', $id)
                     ->where('company_id', $companyId)
                     ->delete();
    }

    public function getEmployeeBenefits(int $employeeId): Collection
    {
        return Benefit::whereHas('employees', function ($query) use ($employeeId) {
                     $query->where('employee_id', $employeeId);
                 })
                 ->with(['pivot' => function ($query) use ($employeeId) {
                     $query->where('employee_id', $employeeId);
                 }])
                 ->get();
    }

    public function assignBenefitToEmployee(int $benefitId, int $employeeId, array $data = []): bool
    {
        $benefit = Benefit::find($benefitId);
        if (!$benefit) {
            return false;
        }
        
        $benefit->employees()->attach($employeeId, $data);
        return true;
    }

    public function removeBenefitFromEmployee(int $benefitId, int $employeeId): bool
    {
        $benefit = Benefit::find($benefitId);
        if (!$benefit) {
            return false;
        }
        
        $benefit->employees()->detach($employeeId);
        return true;
    }

    public function calculateBenefitAmount(int $benefitId, int $employeeId): float
    {
        $benefit = Benefit::find($benefitId);
        if (!$benefit) {
            return 0;
        }
        
        // Implementation depends on benefit type and calculation rules
        return $benefit->amount ?? 0;
    }
}
