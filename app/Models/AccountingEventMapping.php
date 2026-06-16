<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingEventMapping extends Model
{
    protected $fillable = [
        'event_id',
        'debit_account_id',
        'credit_account_id',
        'debit_multiplier',
        'credit_multiplier',
        'sequence',
    ];

    protected $casts = [
        'debit_multiplier' => 'decimal:1',
        'credit_multiplier' => 'decimal:1',
    ];

    // Relationships
    public function event(): BelongsTo
    {
        return $this->belongsTo(AccountingEvent::class, 'event_id');
    }

    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }

    // Scopes
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId)->ordered();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence', 'asc');
    }
}
