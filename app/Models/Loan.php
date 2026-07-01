<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'loan_code',
    'member_id',
    'loan_amount',
    'service_charge',
    'taken_date',
    'due_date',
    'status',
    'purpose',
    'comment',
    'attachments',
    'recorded_by',
    'approved_by',
    'approved_at',
])]
class Loan extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'loan_amount' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'taken_date' => 'date',
            'due_date' => 'date',
            'approved_at' => 'datetime',
            'attachments' => 'array',
        ];
    }

    // --------------------------------------------------------------- Relations

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(LoanStatusHistory::class);
    }

    // ----------------------------------------------------------------- Scopes

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    // -------------------------------------------------------------- Accessors

    /** Total amount the member must pay back (principal + service charge). */
    public function getTotalPayableAttribute(): float
    {
        return (float) $this->loan_amount + (float) $this->service_charge;
    }

    public function getTotalRepaidAttribute(): float
    {
        return (float) $this->repayments->sum('amount');
    }

    /** Remaining balance still owed. Never negative. */
    public function getOutstandingBalanceAttribute(): float
    {
        return max(0, $this->total_payable - $this->total_repaid);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'active'
            && $this->due_date !== null
            && $this->due_date->isPast()
            && $this->outstanding_balance > 0;
    }

    /**
     * A human-facing status that layers derived states (partially repaid,
     * overdue) on top of the stored status.
     */
    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'active') {
            if ($this->is_overdue) {
                return 'overdue';
            }
            if ($this->total_repaid > 0 && $this->outstanding_balance > 0) {
                return 'partially_repaid';
            }
        }

        return $this->status;
    }

    // -------------------------------------------------------------- Behaviour

    /** Record a status transition in the audit trail. */
    public function logStatus(string $to, ?int $userId, ?string $notes = null): void
    {
        LoanStatusHistory::create([
            'loan_id' => $this->id,
            'status_from' => $this->getOriginal('status'),
            'status_to' => $to,
            'changed_by' => $userId ?? auth()->id() ?? $this->recorded_by,
            'notes' => $notes,
            'changed_at' => now(),
        ]);
    }

    /**
     * Refresh the stored status based on the outstanding balance. Marks the
     * loan as repaid once the balance hits zero (and reopens it if a
     * repayment is removed). Only meaningful while a loan is active/repaid.
     */
    public function syncRepaymentStatus(?int $userId = null): void
    {
        if (! in_array($this->status, ['active', 'repaid'], true)) {
            return;
        }

        $newStatus = $this->fresh('repayments')->outstanding_balance <= 0 ? 'repaid' : 'active';

        if ($newStatus !== $this->status) {
            $from = $this->status;
            $this->update(['status' => $newStatus]);
            LoanStatusHistory::create([
                'loan_id' => $this->id,
                'status_from' => $from,
                'status_to' => $newStatus,
                'changed_by' => $userId ?? auth()->id() ?? $this->recorded_by,
                'notes' => $newStatus === 'repaid' ? 'Fully repaid' : 'Reopened after repayment change',
                'changed_at' => now(),
            ]);
        }
    }
}
