<?php

namespace App\Repositories;

use App\Contracts\DocumentRepositoryInterface;
use App\Models\Document;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class DocumentRepository implements DocumentRepositoryInterface
{
    public function getAllDocuments(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Document::where('company_id', $companyId);
        
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        
        return $query->with(['employee', 'uploadedBy'])
                    ->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createDocument(array $data): Document
    {
        return Document::create($data);
    }

    public function findDocument(int $id, int $companyId): ?Document
    {
        return Document::where('id', $id)
                      ->where('company_id', $companyId)
                      ->with(['employee', 'uploadedBy'])
                      ->first();
    }

    public function updateDocument(int $id, array $data, int $companyId): bool
    {
        return Document::where('id', $id)
                      ->where('company_id', $companyId)
                      ->update($data);
    }

    public function deleteDocument(int $id, int $companyId): bool
    {
        $document = $this->findDocument($id, $companyId);
        if (!$document) {
            return false;
        }
        
        // Delete file from storage
        if ($document->file_path && Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }
        
        return $document->delete();
    }

    public function getEmployeeDocuments(int $employeeId, int $companyId): Collection
    {
        return Document::where('employee_id', $employeeId)
                      ->where('company_id', $companyId)
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    public function getDocumentsByType(string $type, int $companyId): Collection
    {
        return Document::where('type', $type)
                      ->where('company_id', $companyId)
                      ->with('employee')
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    public function uploadDocument(array $data, $file): Document
    {
        $path = $file->store('documents', 'public');
        
        return $this->createDocument(array_merge($data, [
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]));
    }
}
