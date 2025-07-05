<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function getCurrentCompanyAttribute()
    {
        return $this->companies()->where('is_default', true)->first()?->company;
    }

    public function hasCompanyAccess(string $companyId): bool
    {
        return $this->companies()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
