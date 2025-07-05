<?php

namespace App\Repositories;

use App\Contracts\PayrollRepositoryInterface;
use App\Models\PayrollPeriod;
use App\Models\PayrollEntry;
use App\Models\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class PayrollRepository implements PayrollRepositoryInterface
{
    public function getAllPeriods(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = PayrollPeriod::where('company_id', $companyId);
        
        if (isset($filters['year'])) {
            $query->whereYear('start_date', $filters['year']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->orderBy('start_date', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createPeriod(array $data): PayrollPeriod
    {
        return PayrollPeriod::create($data);
    }

    public function findPeriod(int $id, int $companyId): ?PayrollPeriod
    {
        return PayrollPeriod::where('id', $id)
                           ->where('company_id', $companyId)
                           ->first();
    }

    public function updatePeriod(int $id, array $data, int $companyId): bool
    {
        return PayrollPeriod::where('id', $id)
                           ->where('company_id', $companyId)
                           ->update($data);
    }

    public function deletePeriod(int $id, int $companyId): bool
    {
        return PayrollPeriod::where('id', $id)
                           ->where('company_id', $companyId)
                           ->delete();
    }

    public function getActivePayrollPeriod(int $companyId): ?PayrollPeriod
    {
        return PayrollPeriod::where('company_id', $companyId)
                           ->where('status', 'active')
                           ->first();
    }

    public function processPayroll(int $periodId, int $companyId): Collection
    {
        $period = $this->findPeriod($periodId, $companyId);
        $employees = Employee::where('company_id', $companyId)
                            ->where('status', 'active')
                            ->get();
        
        $entries = collect();
        foreach ($employees as $employee) {
            $entry = $this->createPayrollEntry([
                'payroll_period_id' => $periodId,
                'employee_id' => $employee->id,
                'basic_salary' => $employee->salary->basic_salary ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $entries->push($entry);
        }
        
        return $entries;
    }

    public function getPayrollEntries(int $periodId, int $companyId): Collection
    {
        return PayrollEntry::whereHas('payrollPeriod', function ($query) use ($companyId) {
                             $query->where('company_id', $companyId);
                         })
                         ->where('payroll_period_id', $periodId)
                         ->with(['employee', 'payrollPeriod'])
                         ->get();
    }

    public function createPayrollEntry(array $data): PayrollEntry
    {
        return PayrollEntry::create($data);
    }

    public function calculateTotalPayroll(int $periodId, int $companyId): float
    {
        return PayrollEntry::whereHas('payrollPeriod', function ($query) use ($companyId) {
                             $query->where('company_id', $companyId);
                         })
                         ->where('payroll_period_id', $periodId)
                         ->sum('net_pay');
    }
}
