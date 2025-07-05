<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCompany
{
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany(Builder $query, string $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    protected static function bootBelongsToCompany()
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->company_id && !$model->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }
}
