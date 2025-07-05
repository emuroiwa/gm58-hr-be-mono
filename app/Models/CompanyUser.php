<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyUser extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'company_users';

    protected $fillable = [
        'company_id',
        'user_id',
        'role',
        'permissions',
        'joined_at',
        'left_at',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
