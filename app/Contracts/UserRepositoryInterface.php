<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getAllUsers(array $filters = []): LengthAwarePaginator;
    public function createUser(array $data): User;
    public function findUser(int $id): ?User;
    public function findUserByEmail(string $email): ?User;
    public function updateUser(int $id, array $data): bool;
    public function deleteUser(int $id): bool;
    public function getUsersByRole(string $role): Collection;
    public function assignRole(int $userId, string $role): bool;
    public function removeRole(int $userId, string $role): bool;
    public function getUserPermissions(int $userId): Collection;
}
