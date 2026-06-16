<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalVoucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'voucher_number',
        'voucher_date',
        'voucher_type',
        'source_module',
        'source_record_id',
        'description',
        'status',
        'posted_date',
        'posted_by',
        'reversed_date',
        'reversed_by',
        'reversal_reason',
        'created_by',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'posted_date' => 'datetime',
        'reversed_date' => 'datetime',
    ];

    // Relationships
    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'voucher_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDraft($query)
    {
        return $query->byStatus('DRAFT');
    }

    public function scopePosted($query)
    {
        return $query->byStatus('POSTED');
    }

    public function scopeReversed($query)
    {
        return $query->byStatus('REVERSED');
    }

    public function scopeByDateRange($query, $fromDate, $toDate)
    {
        return $query->whereDate('voucher_date', '>=', $fromDate)
                     ->whereDate('voucher_date', '<=', $toDate);
    }

    public function scopeBySourceModule($query, $module)
    {
        return $query->where('source_module', $module);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('voucher_number', 'desc');
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isPosted(): bool
    {
        return $this->status === 'POSTED';
    }

    public function isReversed(): bool
    {
        return $this->status === 'REVERSED';
    }

    public function getTotalDebits(): float
    {
        return $this->entries->sum('debit_amount') ?? 0;
    }

    public function getTotalCredits(): float
    {
        return $this->entries->sum('credit_amount') ?? 0;
    }

    public function isBalanced(): bool
    {
        return abs($this->getTotalDebits() - $this->getTotalCredits()) < 0.01;
    }

    public function post(User $user, string $description = null): bool
    {
        if (!$this->isDraft()) {
            return false;
        }

        if (!$this->isBalanced()) {
            return false;
        }

        $this->update([
            'status' => 'POSTED',
            'posted_date' => now(),
            'posted_by' => $user->id,
        ]);

        AccountingAuditLog::create([
            'entity_type' => 'journal_voucher',
            'entity_id' => $this->id,
            'action' => 'POSTED',
            'user_id' => $user->id,
            'new_values' => ['status' => 'POSTED'],
        ]);

        return true;
    }

    public function reverse(User $user, string $reason): bool
    {
        if (!$this->isPosted()) {
            return false;
        }

        $this->update([
            'status' => 'REVERSED',
            'reversed_date' => now(),
            'reversed_by' => $user->id,
            'reversal_reason' => $reason,
        ]);

        AccountingAuditLog::create([
            'entity_type' => 'journal_voucher',
            'entity_id' => $this->id,
            'action' => 'REVERSED',
            'user_id' => $user->id,
            'new_values' => ['status' => 'REVERSED', 'reversal_reason' => $reason],
        ]);

        return true;
    }

    public static function generateVoucherNumber(): string
    {
        $year = now()->year;
        $latestVoucher = self::where('voucher_number', 'like', "JV-{$year}-%")
            ->orderByRaw('CAST(SUBSTRING(voucher_number, -6) AS UNSIGNED) DESC')
            ->first();

        if (!$latestVoucher) {
            $sequence = 1;
        } else {
            $parts = explode('-', $latestVoucher->voucher_number);
            $sequence = (int)end($parts) + 1;
        }

        return 'JV-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
