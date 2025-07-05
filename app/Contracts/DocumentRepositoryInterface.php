<?php

namespace App\Contracts;

use App\Models\Document;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DocumentRepositoryInterface
{
    public function getAllDocuments(int $companyId, array $filters = []): LengthAwarePaginator;
    public function createDocument(array $data): Document;
    public function findDocument(int $id, int $companyId): ?Document;
    public function updateDocument(int $id, array $data, int $companyId): bool;
    public function deleteDocument(int $id, int $companyId): bool;
    public function getEmployeeDocuments(int $employeeId, int $companyId): Collection;
    public function getDocumentsByType(string $type, int $companyId): Collection;
    public function uploadDocument(array $data, $file): Document;
}
