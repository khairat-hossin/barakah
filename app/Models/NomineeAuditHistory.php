<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nominee_id', 'member_id', 'action', 'full_name', 'allocation_percentage', 'total_allocation_after_change', 'changed_by', 'timestamp'])]
class NomineeAuditHistory extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    public function nominee(): BelongsTo
    {
        return $this->belongsTo(Nominee::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId)->orderByDesc('timestamp');
    }
}
