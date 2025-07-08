<?php
// app/Repositories/PayrollRepository.php

namespace App\Repositories;

use App\Models\PayrollPeriod;
use App\Models\Payroll;
use App\Contracts\PayrollRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PayrollRepository implements PayrollRepositoryInterface
{
    public function createPeriod(array $data): PayrollPeriod
    {
        return PayrollPeriod::create($data);
    }

    public function findPeriod(string $id): ?PayrollPeriod
    {
        return PayrollPeriod::with(['payrolls', 'company'])->find($id);
    }

    public function findPeriodByIdAndCompany(string $periodId, string $companyId): ?PayrollPeriod
    {
        return PayrollPeriod::where('id', $periodId)
            ->where('company_id', $companyId)
            ->first();
    }

    public function getPeriodsWithFilters(string $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = PayrollPeriod::where('company_id', $companyId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['year'])) {
            $query->whereYear('start_date', $filters['year']);
        }

        if (isset($filters['month'])) {
            $query->whereMonth('start_date', $filters['month']);
        }

        return $query->orderBy('start_date', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Payroll
    {
        return Payroll::create($data);
    }

    public function getByPeriod(string $periodId): Collection
    {
        return Payroll::where('payroll_period_id', $periodId)
            ->with(['employee', 'currency'])
            ->get();
    }

    public function getPayrollDetailsByPeriod(string $periodId): array
    {
        $period = $this->findPeriod($periodId);
        $payrolls = $this->getByPeriod($periodId);

        return [
            'period' => $period,
            'payrolls' => $payrolls,
            'summary' => [
                'total_employees' => $payrolls->count(),
                'total_gross' => $payrolls->sum('gross_salary'),
                'total_deductions' => $payrolls->sum('total_deductions'),
                'total_net' => $payrolls->sum('net_salary'),
            ],
            'by_department' => $payrolls->groupBy('employee.department_id')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_gross' => $group->sum('gross_salary'),
                    'total_net' => $group->sum('net_salary'),
                ];
            }),
        ];
    }

    public function getPayrollSummaryByCompany(string $companyId): array
    {
        $periods = PayrollPeriod::where('company_id', $companyId)
            ->orderBy('start_date', 'desc')
            ->limit(12)
            ->get();

        return [
            'recent_periods' => $periods,
            'yearly_summary' => $periods->groupBy(function ($period) {
                return $period->start_date->year;
            })->map(function ($yearPeriods) {
                return [
                    'total_periods' => $yearPeriods->count(),
                    'total_gross' => $yearPeriods->sum('total_gross'),
                    'total_net' => $yearPeriods->sum('total_net'),
                ];
            }),
        ];
    }
}