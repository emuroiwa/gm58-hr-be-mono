<?php

namespace App\Services;

use App\Contracts\CompanyRepositoryInterface;
use App\Contracts\DepartmentRepositoryInterface;
use App\Contracts\PositionRepositoryInterface;
use App\Contracts\EmployeeRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class CompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private DepartmentRepositoryInterface $departmentRepository,
        private PositionRepositoryInterface $positionRepository,
        private EmployeeRepositoryInterface $employeeRepository
    ) {}

    public function getCompany($companyId)
    {
        return $this->companyRepository->find($companyId);
    }

    public function updateCompany($companyId, array $data)
    {
        if (isset($data['logo']) && $data['logo']) {
            $logoPath = $this->uploadLogo($data['logo'], $companyId);
            $data['logo'] = $logoPath;
        }

        return $this->companyRepository->update($companyId, $data);
    }

    public function getDepartments($companyId)
    {
        return $this->departmentRepository->getByCompany($companyId);
    }

    public function createDepartment($companyId, array $data)
    {
        return $this->departmentRepository->create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'manager_id' => $data['manager_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updateDepartment($departmentId, $companyId, array $data)
    {
        $department = $this->departmentRepository->findByIdAndCompany($departmentId, $companyId);
        
        if (!$department) {
            throw new \Exception('Department not found');
        }

        return $this->departmentRepository->update($departmentId, $data);
    }

    public function deleteDepartment($departmentId, $companyId)
    {
        $department = $this->departmentRepository->findByIdAndCompany($departmentId, $companyId);
        
        if (!$department) {
            throw new \Exception('Department not found');
        }

        return $this->departmentRepository->delete($departmentId);
    }

    public function getPositions($companyId, $departmentId = null)
    {
        if ($departmentId) {
            return $this->positionRepository->getByDepartment($departmentId);
        }

        return $this->positionRepository->getByCompany($companyId);
    }

    public function createPosition($companyId, array $data)
    {
        return $this->positionRepository->create([
            'company_id' => $companyId,
            'department_id' => $data['department_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'min_salary' => $data['min_salary'] ?? null,
            'max_salary' => $data['max_salary'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updatePosition($positionId, $companyId, array $data)
    {
        $position = $this->positionRepository->findByIdAndCompany($positionId, $companyId);
        
        if (!$position) {
            throw new \Exception('Position not found');
        }

        return $this->positionRepository->update($positionId, $data);
    }

    public function deletePosition($positionId, $companyId)
    {
        $position = $this->positionRepository->findByIdAndCompany($positionId, $companyId);
        
        if (!$position) {
            throw new \Exception('Position not found');
        }

        return $this->positionRepository->delete($positionId);
    }

    public function getCompanyStats($companyId)
    {
        return [
            'total_employees' => $this->employeeRepository->countByCompany($companyId),
            'active_employees' => $this->employeeRepository->countActiveByCompany($companyId),
            'total_departments' => $this->departmentRepository->countByCompany($companyId),
            'total_positions' => $this->positionRepository->countByCompany($companyId),
            'employees_on_leave' => $this->employeeRepository->countOnLeave($companyId),
        ];
    }

    private function uploadLogo($file, $companyId)
    {
        $filename = 'company_' . $companyId . '_logo.' . $file->getClientOriginalExtension();
        return $file->storeAs('logos', $filename, 'public');
    }
}
