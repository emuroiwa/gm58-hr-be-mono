<?php

namespace App\Repositories;

use App\Contracts\CompanyUserRepositoryInterface;
use App\Models\CompanyUser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CompanyUserRepository implements CompanyUserRepositoryInterface
{
    public function getCompanyUsers(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = CompanyUser::where('company_id', $companyId);
        
        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->with(['user', 'company'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function addUserToCompany(int $userId, int $companyId, array $data = []): CompanyUser
    {
        return CompanyUser::create(array_merge([
            'user_id' => $userId,
            'company_id' => $companyId,
        ], $data));
    }

    public function removeUserFromCompany(int $userId, int $companyId): bool
    {
        return CompanyUser::where('user_id', $userId)
                         ->where('company_id', $companyId)
                         ->delete();
    }

    public function updateCompanyUser(int $userId, int $companyId, array $data): bool
    {
        return CompanyUser::where('user_id', $userId)
                         ->where('company_id', $companyId)
                         ->update($data);
    }

    public function findCompanyUser(int $userId, int $companyId): ?CompanyUser
    {
        return CompanyUser::where('user_id', $userId)
                         ->where('company_id', $companyId)
                         ->with(['user', 'company'])
                         ->first();
    }

    public function getUserCompanies(int $userId): Collection
    {
        return CompanyUser::where('user_id', $userId)
                         ->with('company')
                         ->get();
    }

    public function isUserInCompany(int $userId, int $companyId): bool
    {
        return CompanyUser::where('user_id', $userId)
                         ->where('company_id', $companyId)
                         ->exists();
    }
}
