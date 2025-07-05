<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
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
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }
        
        return $query->with('roles')
                    ->orderBy('name')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function findUser(int $id): ?User
    {
        return User::with(['roles', 'permissions'])->find($id);
    }

    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function updateUser(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function deleteUser(int $id): bool
    {
        return User::where('id', $id)->delete();
    }

    public function getUsersByRole(string $role): Collection
    {
        return User::whereHas('roles', function ($query) use ($role) {
                   $query->where('name', $role);
               })
               ->get();
    }

    public function assignRole(int $userId, string $role): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        
        $user->assignRole($role);
        return true;
    }

    public function removeRole(int $userId, string $role): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }
        
        $user->removeRole($role);
        return true;
    }

    public function getUserPermissions(int $userId): Collection
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }
        
        return $user->getAllPermissions();
    }
}
