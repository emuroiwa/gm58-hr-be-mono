<?php

namespace App\Repositories;

use App\Models\Company;
use App\Contracts\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function find(string $id): ?Company
    {
        return Company::with(['baseCurrency', 'settings'])->find($id);
    }

    public function findByCode(string $code): ?Company
    {
        return Company::where('code', $code)->first();
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(string $id, array $data): Company
    {
        $company = Company::findOrFail($id);
        $company->update($data);
        return $company->fresh(['baseCurrency', 'settings']);
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return QueryBuilder::for(Company::class)
            ->allowedFilters(['is_active', 'billing_plan', 'industry'])
            ->allowedSorts(['name', 'created_at'])
            ->with(['baseCurrency'])
            ->paginate(request('per_page', 15));
    }

    public function getUserCompanies(string $userId): LengthAwarePaginator
    {
        return Company::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('is_active', true);
        })
        ->with(['baseCurrency'])
        ->paginate(request('per_page', 15));
    }
}
