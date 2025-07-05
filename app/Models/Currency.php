<?php

namespace App\Models;

class Currency extends BaseModel
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'is_active',
        'is_base_currency',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_base_currency' => 'boolean',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'base_currency_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBase($query)
    {
        return $query->where('is_base_currency', true);
    }
}
