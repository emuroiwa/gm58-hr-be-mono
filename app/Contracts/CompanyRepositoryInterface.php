<?php

namespace App\Contracts;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    public function find(string $id): ?Company;
    public function findByCode(string $code): ?Company;
    public function create(array $data): Company;
    public function update(string $id, array $data): Company;
    public function getAll(array $filters = []): LengthAwarePaginator;
    public function getUserCompanies(string $userId): LengthAwarePaginator;
}
