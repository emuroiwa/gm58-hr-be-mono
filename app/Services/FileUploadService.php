<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public function uploadAvatar(UploadedFile $file, $employeeId)
    {
        $filename = 'avatar_' . $employeeId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('avatars', $filename, 'public');
    }

    public function uploadDocument(UploadedFile $file, $employeeId, $type = 'general')
    {
        $filename = $type . '_' . $employeeId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('documents', $filename, 'public');
    }

    public function uploadCompanyLogo(UploadedFile $file, $companyId)
    {
        $filename = 'logo_' . $companyId . '_' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('logos', $filename, 'public');
    }

    public function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    public function getFileUrl($path)
    {
        return Storage::disk('public')->url($path);
    }
}
