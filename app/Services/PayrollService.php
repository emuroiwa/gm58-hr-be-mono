<?php

namespace App\Services;

use App\Contracts\PayrollRepositoryInterface;
use App\Contracts\EmployeeRepositoryInterface;
use App\Contracts\AttendanceRepositoryInterface;
use App\Jobs\ProcessPayroll;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Events\PayrollProcessed;

class PayrollService
{
    public function __construct(
        private PayrollRepositoryInterface $payrollRepository,
        private EmployeeRepositoryInterface $employeeRepository,
        private AttendanceRepositoryInterface $attendanceRepository
    ) {}

    public function getPayrollPeriods($companyId, array $filters = [])
    {
        return $this->payrollRepository->getPeriodsWithFilters($companyId, $filters);
    }

    public function createPayrollPeriod($companyId, array $data)
    {
        return $this->payrollRepository->createPeriod([
            'company_id' => $companyId,
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'pay_date' => $data['pay_date'],
            'status' => 'draft',
            'total_gross' => 0,
            'total_deductions' => 0,
            'total_net' => 0,
        ]);
    }

    public function processPayrollPeriod($periodId, $companyId)
    {
        $period = $this->payrollRepository->findPeriodByIdAndCompany($periodId, $companyId);
        
        if (!$period) {
            throw new \Exception('Payroll period not found');
        }

        if ($period->status !== 'draft') {
            throw new \Exception('Payroll period can only be processed when in draft status');
        }

        // Dispatch job for background processing
        ProcessPayroll::dispatch($period);

        $this->payrollRepository->updatePeriod($periodId, ['status' => 'processing']);

        return [
            'message' => 'Payroll processing started', 
            'period' => $this->payrollRepository->findPeriod($periodId)
        ];
    }

    public function calculatePayroll($period)
    {
        return DB::transaction(function () use ($period) {
            $employees = $this->employeeRepository->getActiveEmployeesByCompany($period->company_id);

            $totalGross = 0;
            $totalDeductions = 0;
            $totalNet = 0;

            foreach ($employees as $employee) {
                $payrollData = $this->calculateEmployeePayroll($employee, $period);
                
                $this->payrollRepository->createPayroll([
                    'company_id' => $period->company_id,
                    'employee_id' => $employee->id,
                    'payroll_period_id' => $period->id,
                    'basic_salary' => $payrollData['basic_salary'],
                    'overtime_amount' => $payrollData['overtime_amount'],
                    'bonus_amount' => $payrollData['bonus_amount'],
                    'allowances' => $payrollData['allowances'],
                    'gross_pay' => $payrollData['gross_pay'],
                    'tax_amount' => $payrollData['tax_amount'],
                    'deductions' => $payrollData['deductions'],
                    'net_pay' => $payrollData['net_pay'],
                    'working_days' => $payrollData['working_days'],
                    'worked_days' => $payrollData['worked_days'],
                    'status' => 'calculated',
                ]);

                $totalGross += $payrollData['gross_pay'];
                $totalDeductions += $payrollData['tax_amount'] + $payrollData['deductions'];
                $totalNet += $payrollData['net_pay'];
            }

            $this->payrollRepository->updatePeriod($period->id, [
                'total_gross' => $totalGross,
                'total_deductions' => $totalDeductions,
                'total_net' => $totalNet,
                'status' => 'calculated',
                'processed_at' => now(),
            ]);

            $updatedPeriod = $this->payrollRepository->findPeriod($period->id);
            event(new PayrollProcessed($updatedPeriod));

            return $updatedPeriod;
        });
    }

    public function getEmployeePayrolls($employeeId, array $filters = [])
    {
        return $this->payrollRepository->getEmployeePayrolls($employeeId, $filters);
    }

    public function getPayrollDetails($payrollId, $companyId)
    {
        return $this->payrollRepository->findPayrollByIdAndCompany($payrollId, $companyId);
    }

    private function calculateEmployeePayroll($employee, $period)
    {
        $basicSalary = $employee->salary ?? 0;
        $workingDays = $this->getWorkingDays($period->start_date, $period->end_date);
        $workedDays = $this->attendanceRepository->getWorkedDays($employee->id, $period->start_date, $period->end_date);
        
        // Calculate basic pay based on worked days
        $dailyRate = $basicSalary / $workingDays;
        $basicPay = $dailyRate * $workedDays;
        
        // Calculate overtime
        $overtimeAmount = $this->calculateOvertime($employee, $period->start_date, $period->end_date);
        
        // Calculate allowances and bonuses
        $allowances = $this->calculateAllowances($employee);
        $bonusAmount = $this->calculateBonus($employee, $period);
        
        // Gross pay
        $grossPay = $basicPay + $overtimeAmount + $bonusAmount + $allowances;
        
        // Calculate deductions
        $taxAmount = $this->calculateTax($grossPay);
        $deductions = $this->calculateDeductions($employee);
        
        // Net pay
        $netPay = $grossPay - $taxAmount - $deductions;

        return [
            'basic_salary' => $basicSalary,
            'overtime_amount' => $overtimeAmount,
            'bonus_amount' => $bonusAmount,
            'allowances' => $allowances,
            'gross_pay' => $grossPay,
            'tax_amount' => $taxAmount,
            'deductions' => $deductions,
            'net_pay' => $netPay,
            'working_days' => $workingDays,
            'worked_days' => $workedDays,
        ];
    }

    private function getWorkingDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $workingDays = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    private function calculateOvertime($employee, $startDate, $endDate)
    {
        return 0; // Implementation needed
    }

    private function calculateAllowances($employee)
    {
        return 0; // Implementation needed
    }

    private function calculateBonus($employee, $period)
    {
        return 0; // Implementation needed
    }

    private function calculateTax($grossPay)
    {
        return $grossPay * 0.1; // 10% tax rate as example
    }

    private function calculateDeductions($employee)
    {
        return 0; // Implementation needed
    }
}
