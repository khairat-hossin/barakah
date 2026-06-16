<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'investment_id',
    'status_from',
    'status_to',
    'reason',
    'notes',
    'changed_by',
    'changed_at',
    'metadata',
])]
class InvestmentStatusHistory extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
            'metadata' => 'json',
        ];
    }

    // Relationships
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeByInvestment($query, $investmentId)
    {
        return $query->where('investment_id', $investmentId);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('changed_at');
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('changed_at');
    }
}
