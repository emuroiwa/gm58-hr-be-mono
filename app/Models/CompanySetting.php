<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'key',
        'value',
        'type',
        'group',
        'is_encrypted',
        'description',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'value' => 'string',
    ];

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEncrypted($query)
    {
        return $query->where('is_encrypted', true);
    }

    public function getValueAttribute($value)
    {
        if ($this->is_encrypted && $value) {
            return decrypt($value);
        }

        return $this->castValue($value);
    }

    public function setValueAttribute($value)
    {
        if ($this->is_encrypted) {
            $this->attributes['value'] = encrypt($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    protected function castValue($value)
    {
        return match ($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'array' => json_decode($value, true),
            'object' => json_decode($value),
            default => $value,
        };
    }

    public static function get(string $key, $default = null, ?string $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()?->company_id;
        
        if (!$companyId) {
            return $default;
        }

        $setting = self::where('company_id', $companyId)
                      ->where('key', $key)
                      ->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, ?string $companyId = null, array $options = [])
    {
        $companyId = $companyId ?? auth()->user()?->company_id;
        
        if (!$companyId) {
            return false;
        }

        $type = $options['type'] ?? 'string';
        $group = $options['group'] ?? 'general';
        $isEncrypted = $options['is_encrypted'] ?? false;
        $description = $options['description'] ?? null;

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $type = is_array($value) ? 'array' : 'object';
        }

        return self::updateOrCreate(
            ['company_id' => $companyId, 'key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'is_encrypted' => $isEncrypted,
                'description' => $description,
            ]
        );
    }

    public static function forget(string $key, ?string $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()?->company_id;
        
        if (!$companyId) {
            return false;
        }

        return self::where('company_id', $companyId)
                  ->where('key', $key)
                  ->delete();
    }

    public static function getGroup(string $group, ?string $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()?->company_id;
        
        if (!$companyId) {
            return [];
        }

        return self::where('company_id', $companyId)
                  ->where('group', $group)
                  ->pluck('value', 'key')
                  ->toArray();
    }
}