<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'member_id',
    'recorded_by',
    'amount',
    'deposit_date',
    'contribution_month',
    'payment_method',
    'reference',
    'transaction_id',
    'notes',
    'attachments',
])]
class SavingsEntry extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'deposit_date' => 'date',
            'contribution_month' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
