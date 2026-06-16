<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'expense_id',
    'status_from',
    'status_to',
    'changed_by',
    'notes',
    'changed_at',
])]
class ExpenseStatusHistory extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function scopeByExpense($query, $expenseId)
    {
        return $query->where('expense_id', $expenseId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('changed_at', 'desc');
    }
}
