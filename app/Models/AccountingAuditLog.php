<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'user_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'timestamp',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'timestamp' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                     ->where('entity_id', $entityId)
                     ->orderBy('timestamp', 'desc');
    }

    public function scopeByEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereDate('timestamp', '>=', $fromDate)
                     ->whereDate('timestamp', '<=', $toDate);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('timestamp', 'desc');
    }

    // Helper Methods
    public function isCreate(): bool
    {
        return $this->action === 'CREATED';
    }

    public function isPosted(): bool
    {
        return $this->action === 'POSTED';
    }

    public function isReversed(): bool
    {
        return $this->action === 'REVERSED';
    }

    public function isUpdated(): bool
    {
        return $this->action === 'UPDATED';
    }

    public static function log(string $entityType, int $entityId, string $action, User $user, array $oldValues = null, array $newValues = null): self
    {
        return self::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'user_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);
    }
}
