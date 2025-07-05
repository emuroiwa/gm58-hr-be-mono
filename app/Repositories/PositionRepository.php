<?php

namespace App\Repositories;

use App\Contracts\PositionRepositoryInterface;
use App\Models\Position;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PositionRepository implements PositionRepositoryInterface
{
    public function getAllPositions(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Position::where('company_id', $companyId);
        
        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        
        if (isset($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->with('department')
                    ->withCount('employees')
                    ->orderBy('title')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createPosition(array $data): Position
    {
        return Position::create($data);
    }

    public function findPosition(int $id, int $companyId): ?Position
    {
        return Position::where('id', $id)
                      ->where('company_id', $companyId)
                      ->with('department')
                      ->first();
    }

    public function updatePosition(int $id, array $data, int $companyId): bool
    {
        return Position::where('id', $id)
                      ->where('company_id', $companyId)
                      ->update($data);
    }

    public function deletePosition(int $id, int $companyId): bool
    {
        return Position::where('id', $id)
                      ->where('company_id', $companyId)
                      ->delete();
    }

    public function getPositionsByDepartment(int $departmentId, int $companyId): Collection
    {
        return Position::where('department_id', $departmentId)
                      ->where('company_id', $companyId)
                      ->orderBy('title')
                      ->get();
    }

    public function getAvailablePositions(int $companyId): Collection
    {
        return Position::where('company_id', $companyId)
                      ->where('status', 'active')
                      ->with('department')
                      ->orderBy('title')
                      ->get();
    }
}
