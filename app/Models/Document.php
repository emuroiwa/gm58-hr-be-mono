<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'documentable_type',
        'documentable_id',
        'employee_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'mime_type',
        'category',
        'is_confidential',
        'is_required',
        'expires_at',
        'uploaded_by',
        'approved_by',
        'approved_at',
        'status',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_confidential' => 'boolean',
        'is_required' => 'boolean',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeConfidential($query)
    {
        return $query->where('is_confidential', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeExpiringToon($query, int $days = 30)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
                    ->where('expires_at', '>', now());
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at && 
               $this->expires_at <= now()->addDays($days) && 
               $this->expires_at > now();
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
            $this->save();
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $this->tags = array_values(array_diff($tags, [$tag]));
        $this->save();
    }
}