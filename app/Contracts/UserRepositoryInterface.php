<?php
// app/Contracts/UserRepositoryInterface.php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    // Basic CRUD methods
    public function create(array $data): User;
    public function find(string $id): ?User;
    public function findByEmail(string $email): ?User;
    public function update(string $id, array $data): bool;
    public function delete(string $id): bool;
    
    // Company-specific methods
    public function findByIdAndCompany(string $userId, string $companyId): ?User;
    public function updateByIdAndCompany(string $userId, string $companyId, array $data): User;
    public function deleteByIdAndCompany(string $userId, string $companyId): bool;
    public function getByCompanyWithFilters(string $companyId, array $filters = []): LengthAwarePaginator;
    
    // Auth-specific methods
    public function updateLastLogin(string $userId): bool;
    
    // Legacy methods for compatibility
    public function getAllUsers(array $filters = []): LengthAwarePaginator;
    public function findUser(string $id): ?User;
    public function findUserByEmail(string $email): ?User;
    public function createUser(array $data): User;
    public function updateUser(string $id, array $data): bool;
    public function deleteUser(string $id): bool;
    
    // Role and permission methods
    public function getUsersByRole(string $role): Collection;
    public function assignRole(string $userId, string $role): bool;
    public function removeRole(string $userId, string $role): bool;
    public function getUserPermissions(string $userId): Collection;
    public function findWithRelations(string $id, array $relations = []): ?User;
}