<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'expense_number',
    'category_id',
    'member_id',
    'title',
    'description',
    'amount',
    'expense_date',
    'fund_source',
    'payment_method',
    'status',
    'created_by',
    'approved_by',
    'approved_at',
    'paid_at',
    'notes',
])]
class Expense extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
            'approved_at' => 'timestamp',
            'paid_at' => 'timestamp',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ExpenseAttachment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ExpenseStatusHistory::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByFundSource($query, $source)
    {
        return $query->where('fund_source', $source);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function canApprove($user): bool
    {
        return $this->status === 'pending' && $user->can('approve', 'expenses');
    }

    public function canMarkAsPaid($user): bool
    {
        return $this->status === 'approved' && $user->can('manage', 'expenses');
    }

    public function canEdit($user): bool
    {
        return $this->status === 'draft' && $this->created_by === $user->id;
    }

    public function canDelete($user): bool
    {
        return $this->status === 'draft' && $this->created_by === $user->id;
    }

    public function approve($userId, $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        ExpenseStatusHistory::create([
            'expense_id' => $this->id,
            'status_from' => 'pending',
            'status_to' => 'approved',
            'changed_by' => $userId,
            'notes' => $notes,
            'changed_at' => now(),
        ]);
    }

    public function markAsPaid($userId): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        ExpenseStatusHistory::create([
            'expense_id' => $this->id,
            'status_from' => 'approved',
            'status_to' => 'paid',
            'changed_by' => $userId,
            'changed_at' => now(),
        ]);
    }
}
