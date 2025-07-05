<?php

namespace App\Contracts;

use App\Models\Position;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PositionRepositoryInterface
{
    public function getAllPositions(int $companyId, array $filters = []): LengthAwarePaginator;
    public function createPosition(array $data): Position;
    public function findPosition(int $id, int $companyId): ?Position;
    public function updatePosition(int $id, array $data, int $companyId): bool;
    public function deletePosition(int $id, int $companyId): bool;
    public function getPositionsByDepartment(int $departmentId, int $companyId): Collection;
    public function getAvailablePositions(int $companyId): Collection;
}
