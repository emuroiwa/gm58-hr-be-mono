<?php
// app/Repositories/UserRepository.php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Create a new user
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find user by ID
     */
    public function find(string $id): ?User
    {
        return User::with(['employee', 'company'])->find($id);
    }

    /**
     * Find user by ID with specific relations
     */
    public function findWithRelations(string $id, array $relations = []): ?User
    {
        return User::with($relations)->find($id);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Update user
     */
    public function update(string $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    /**
     * Delete user
     */
    public function delete(string $id): bool
    {
        return User::destroy($id) > 0;
    }

    /**
     * Find user by ID and company
     */
    public function findByIdAndCompany(string $userId, string $companyId): ?User
    {
        return User::where('id', $userId)
            ->where('company_id', $companyId)
            ->with(['employee', 'company'])
            ->first();
    }

    /**
     * Update user by ID and company
     */
    public function updateByIdAndCompany(string $userId, string $companyId, array $data): User
    {
        $user = $this->findByIdAndCompany($userId, $companyId);
        
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        $user->update($data);
        return $user->fresh(['employee', 'company']);
    }

    /**
     * Delete user by ID and company
     */
    public function deleteByIdAndCompany(string $userId, string $companyId): bool
    {
        return User::where('id', $userId)
            ->where('company_id', $companyId)
            ->delete() > 0;
    }

    /**
     * Get users by company with filters
     */
    public function getByCompanyWithFilters(string $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = User::where('company_id', $companyId)
            ->with(['employee', 'company']);
        
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('employee', function ($eq) use ($filters) {
                      $eq->where('first_name', 'like', '%' . $filters['search'] . '%')
                         ->orWhere('last_name', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }
        
        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Update user's last login timestamp
     */
    public function updateLastLogin(string $userId): bool
    {
        return User::where('id', $userId)->update([
            'last_login_at' => now()
        ]);
    }

    /**
     * Get all users (legacy method)
     */
    public function getAllUsers(array $filters = []): LengthAwarePaginator
    {
        $query = User::query();
        
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        
        return $query->with(['employee', 'company'])
                    ->orderBy('name')
                    ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Legacy method - delegates to find
     */
    public function findUser(string $id): ?User
    {
        return $this->find($id);
    }

    /**
     * Legacy method - delegates to findByEmail
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->findByEmail($email);
    }

    /**
     * Legacy method - delegates to create
     */
    public function createUser(array $data): User
    {
        return $this->create($data);
    }

    /**
     * Legacy method - delegates to update
     */
    public function updateUser(string $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Legacy method - delegates to delete
     */
    public function deleteUser(string $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        return User::where('role', $role)
            ->with(['employee', 'company'])
            ->get();
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $userId, string $role): bool
    {
        return User::where('id', $userId)->update(['role' => $role]);
    }

    /**
     * Remove role from user (set to default)
     */
    public function removeRole(string $userId, string $role): bool
    {
        return User::where('id', $userId)->update(['role' => 'employee']);
    }

    /**
     * Get user permissions
     */
    public function getUserPermissions(string $userId): Collection
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }
        
        // If using Spatie permissions package
        // return $user->getAllPermissions();
        
        // For now, return role-based permissions
        $rolePermissions = [
            'admin' => collect(['*']),
            'manager' => collect([
                'employees.view', 'employees.create', 'employees.update',
                'departments.view', 'attendance.view', 'leaves.approve'
            ]),
            'employee' => collect([
                'profile.view', 'profile.update',
                'attendance.create', 'leaves.create'
            ])
        ];
        
        return $rolePermissions[$user->role] ?? collect();
    }
}