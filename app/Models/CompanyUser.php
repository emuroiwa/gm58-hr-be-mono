<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyUser extends BaseModel
{
    protected $fillable = [
        'company_id',
        'user_id',
        'role',
        'is_default',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === 'company_admin';
    }

    public function isHR(): bool
    {
        return in_array($this->role, ['company_admin', 'hr']);
    }
}
