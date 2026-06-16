<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'voucher_id',
        'account_id',
        'debit_amount',
        'credit_amount',
        'description',
        'entry_sequence',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    // Relationships
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(JournalVoucher::class, 'voucher_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // Scopes
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByVoucher($query, $voucherId)
    {
        return $query->where('voucher_id', $voucherId);
    }

    public function scopeDebits($query)
    {
        return $query->whereNotNull('debit_amount');
    }

    public function scopeCredits($query)
    {
        return $query->whereNotNull('credit_amount');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('entry_sequence', 'asc');
    }

    // Helper Methods
    public function isDebit(): bool
    {
        return $this->debit_amount !== null && $this->debit_amount > 0;
    }

    public function isCredit(): bool
    {
        return $this->credit_amount !== null && $this->credit_amount > 0;
    }

    public function getAmount(): float
    {
        return $this->debit_amount ?? $this->credit_amount ?? 0;
    }

    public function getType(): string
    {
        return $this->isDebit() ? 'DEBIT' : 'CREDIT';
    }
}
