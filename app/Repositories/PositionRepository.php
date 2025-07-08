<?php
// app/Repositories/PositionRepository.php

namespace App\Repositories;

use App\Models\Position;
use App\Contracts\PositionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PositionRepository implements PositionRepositoryInterface
{
    public function getAllPositions(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Position::where('company_id', $companyId)
            ->with(['department', 'employees']);

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('title')
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
            ->with(['department', 'employees'])
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
            ->delete() > 0;
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

    // Legacy methods for backward compatibility
    public function create(array $data): Position
    {
        return $this->createPosition($data);
    }

    public function find(string $id): ?Position
    {
        return Position::with(['department', 'employees'])->find($id);
    }

    public function update(string $id, array $data): Position
    {
        $position = Position::findOrFail($id);
        $position->update($data);
        return $position->fresh(['department']);
    }

    public function delete(string $id): bool
    {
        return Position::destroy($id) > 0;
    }

    public function getByCompany(string $companyId): Collection
    {
        return Position::where('company_id', $companyId)
            ->with('department')
            ->orderBy('title')
            ->get();
    }

    public function getByDepartment(string $departmentId): Collection
    {
        return Position::where('department_id', $departmentId)
            ->orderBy('title')
            ->get();
    }

    public function findByIdAndCompany(string $positionId, string $companyId): ?Position
    {
        return Position::where('id', $positionId)
            ->where('company_id', $companyId)
            ->with('department')
            ->first();
    }
}