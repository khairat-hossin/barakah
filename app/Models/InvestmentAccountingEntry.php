<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'investment_id',
    'transaction_id',
    'journal_entry_number',
    'entry_type',
    'account_code',
    'account_name',
    'amount',
    'currency_code',
    'posting_status',
    'posted_at',
    'external_reference',
    'metadata',
    'created_by',
])]
class InvestmentAccountingEntry extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'posted_at' => 'datetime',
            'metadata' => 'json',
        ];
    }

    // Relationships
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(InvestmentTransaction::class, 'transaction_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByInvestment($query, $investmentId)
    {
        return $query->where('investment_id', $investmentId);
    }

    public function scopeByPostingStatus($query, $status)
    {
        return $query->where('posting_status', $status);
    }

    public function scopeDraft($query)
    {
        return $query->where('posting_status', 'draft');
    }

    public function scopePosted($query)
    {
        return $query->where('posting_status', 'posted');
    }

    // Methods
    public function canPost(): bool
    {
        return $this->posting_status === 'draft';
    }

    public function post(): void
    {
        if (!$this->canPost()) {
            throw new \Exception("Cannot post an entry with status: {$this->posting_status}");
        }

        $this->update([
            'posting_status' => 'posted',
            'posted_at' => now(),
        ]);
    }

    public function reverse(): void
    {
        if ($this->posting_status !== 'posted') {
            throw new \Exception("Can only reverse posted entries");
        }

        $this->update([
            'posting_status' => 'reversed',
        ]);
    }
}
