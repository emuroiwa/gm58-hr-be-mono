<?php

namespace App\Contracts;

use App\Models\CompanyUser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CompanyUserRepositoryInterface
{
    public function getCompanyUsers(int $companyId, array $filters = []): LengthAwarePaginator;
    public function addUserToCompany(int $userId, int $companyId, array $data = []): CompanyUser;
    public function removeUserFromCompany(int $userId, int $companyId): bool;
    public function updateCompanyUser(int $userId, int $companyId, array $data): bool;
    public function findCompanyUser(int $userId, int $companyId): ?CompanyUser;
    public function getUserCompanies(int $userId): Collection;
    public function isUserInCompany(int $userId, int $companyId): bool;
}
