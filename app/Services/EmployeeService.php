<?php

namespace App\Services;

use App\Models\Employee;
use App\Contracts\EmployeeRepositoryInterface;
use App\Events\EmployeeCreated;
use App\Events\EmployeeUpdated;
use App\Events\EmployeeDeleted;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepository
    ) {}

    public function getEmployeesByCompany(string $companyId, array $filters = []): LengthAwarePaginator
    {
        return $this->employeeRepository->findByCompany($companyId, $filters);
    }

    public function getEmployee(string $id, string $companyId): ?Employee
    {
        return $this->employeeRepository->findByIdAndCompany($id, $companyId);
    }

    public function createEmployee(array $data, string $companyId): Employee
    {
        $data['company_id'] = $companyId;
        
        if (empty($data['employee_number'])) {
            $data['employee_number'] = $this->employeeRepository->generateEmployeeNumber($companyId);
        }

        $employee = $this->employeeRepository->create($data);
        
        event(new EmployeeCreated($employee));
        
        return $employee;
    }

    public function updateEmployee(string $id, array $data, string $companyId): Employee
    {
        $employee = $this->employeeRepository->findByIdAndCompany($id, $companyId);
        
        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        $updatedEmployee = $this->employeeRepository->update($id, $data);
        
        event(new EmployeeUpdated($updatedEmployee));
        
        return $updatedEmployee;
    }

    public function deleteEmployee(string $id, string $companyId): bool
    {
        $employee = $this->employeeRepository->findByIdAndCompany($id, $companyId);
        
        if (!$employee) {
            throw new \Exception('Employee not found');
        }

        $result = $this->employeeRepository->delete($id);
        
        if ($result) {
            event(new EmployeeDeleted($employee));
        }
        
        return $result;
    }
}
