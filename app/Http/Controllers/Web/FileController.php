<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Handle file upload
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|in:avatar,document,logo',
            'employee_id' => 'sometimes|exists:employees,id'
        ]);

        try {
            $file = $request->file('file');
            $type = $request->get('type');
            $employeeId = $request->get('employee_id', $request->user()->employee?->id);

            switch ($type) {
                case 'avatar':
                    $path = $this->fileUploadService->uploadAvatar($file, $employeeId);
                    break;
                    
                case 'document':
                    $path = $this->fileUploadService->uploadDocument($file, $employeeId, 'general');
                    break;
                    
                case 'logo':
                    $path = $this->fileUploadService->uploadCompanyLogo($file, $request->user()->company_id);
                    break;
                    
                default:
                    return response()->json(['error' => 'Invalid file type'], 422);
            }

            return response()->json([
                'message' => 'File uploaded successfully',
                'path' => $path,
                'url' => $this->fileUploadService->getFileUrl($path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file
     */
    public function download(Request $request, string $fileId): mixed
    {
        try {
            // This would typically look up a file record by ID
            // For now, assume fileId is the storage path
            
            if (!Storage::exists($fileId)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Verify user has access to this file
            $user = $request->user();
            
            // Simple access check - file path should contain company ID
            if (!str_contains($fileId, "company_{$user->company_id}") && 
                !str_contains($fileId, "employee_{$user->employee?->id}")) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            return Storage::download($fileId);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function delete(Request $request, string $fileId): JsonResponse
    {
        try {
            // Verify user has permission to delete this file
            $user = $request->user();
            
            if (!in_array($user->role, ['admin', 'hr']) && 
                !str_contains($fileId, "employee_{$user->employee?->id}")) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $deleted = $this->fileUploadService->deleteFile($fileId);

            if ($deleted) {
                return response()->json(['message' => 'File deleted successfully']);
            } else {
                return response()->json(['error' => 'File not found'], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
