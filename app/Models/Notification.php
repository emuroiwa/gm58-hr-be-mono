<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'employee_id',
        'type',
        'title',
        'message',
        'data',
        'notifiable_type',
        'notifiable_id',
        'channel',
        'priority',
        'is_read',
        'read_at',
        'scheduled_for',
        'sent_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_for');
    }

    public function scopeDue($query)
    {
        return $query->where('scheduled_for', '<=', now())
                    ->where('status', 'pending');
    }

    public function scopeHigh($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeMedium($query)
    {
        return $query->where('priority', 'medium');
    }

    public function scopeLow($query)
    {
        return $query->where('priority', 'low');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function isRead(): bool
    {
        return $this->is_read;
    }

    public function isUnread(): bool
    {
        return !$this->is_read;
    }

    public function isScheduled(): bool
    {
        return $this->scheduled_for !== null;
    }

    public function isDue(): bool
    {
        return $this->scheduled_for && $this->scheduled_for <= now();
    }

    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    public function isMediumPriority(): bool
    {
        return $this->priority === 'medium';
    }

    public function isLowPriority(): bool
    {
        return $this->priority === 'low';
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'leave_request' => 'calendar',
            'payroll' => 'dollar-sign',
            'attendance' => 'clock',
            'training' => 'book',
            'performance' => 'star',
            'document' => 'file',
            'system' => 'settings',
            default => 'bell',
        };
    }
}