<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'transaction_number',
    'investment_id',
    'transaction_type',
    'transaction_date',
    'amount',
    'reference_number',
    'description',
    'status',
    'approved_by',
    'approved_at',
    'metadata',
    'created_by',
])]
class InvestmentTransaction extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'metadata' => 'json',
        ];
    }

    // Relationships
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function accountingEntry(): HasOne
    {
        return $this->hasOne(InvestmentAccountingEntry::class, 'transaction_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeByInvestment($query, $investmentId)
    {
        return $query->where('investment_id', $investmentId);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    // Methods
    public function approve(User $user, ?string $notes = null): void
    {
        $this->update([
            'status' => 'processed',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
    }

    public function reject(?string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], ['rejection_reason' => $reason]),
        ]);
    }

    public function reverse(?string $reason = null): void
    {
        $this->update([
            'status' => 'reversed',
            'metadata' => array_merge($this->metadata ?? [], ['reversal_reason' => $reason]),
        ]);
    }
}
