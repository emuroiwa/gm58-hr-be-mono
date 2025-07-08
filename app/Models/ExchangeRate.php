<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'from_currency_id',
        'to_currency_id',
        'rate',
        'date',
        'source',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForCurrencyPair($query, string $fromCurrency, string $toCurrency)
    {
        return $query->where('from_currency_id', $fromCurrency)
                    ->where('to_currency_id', $toCurrency);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days));
    }

    public function convert(float $amount): float
    {
        return $amount * $this->rate;
    }

    public function getInverseRateAttribute(): float
    {
        return $this->rate > 0 ? 1 / $this->rate : 0;
    }

    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    public function isRecent(int $days = 7): bool
    {
        return $this->date >= now()->subDays($days);
    }

    public function getAgeInDaysAttribute(): int
    {
        return now()->diffInDays($this->date);
    }

    public static function getLatestRate(string $fromCurrency, string $toCurrency): ?float
    {
        $rate = self::where('from_currency_id', $fromCurrency)
                   ->where('to_currency_id', $toCurrency)
                   ->where('is_active', true)
                   ->latest('date')
                   ->first();

        return $rate ? $rate->rate : null;
    }

    public static function convertAmount(float $amount, string $fromCurrency, string $toCurrency): ?float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rate = self::getLatestRate($fromCurrency, $toCurrency);
        
        return $rate ? $amount * $rate : null;
    }
}