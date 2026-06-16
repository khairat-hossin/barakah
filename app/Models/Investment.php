<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code',
    'investment_type_id',
    'investor_id',
    'name',
    'description',
    'status',
    'risk_level',
    'return_type',
    'expected_return_percentage',
    'actual_return_percentage',
    'tenure_months',
    'start_date',
    'maturity_date',
    'closed_date',
    'total_invested_amount',
    'total_returned_amount',
    'net_profit_loss',
    'notes',
    'metadata',
    'created_by',
    'updated_by',
])]
class Investment extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'maturity_date' => 'date',
            'closed_date' => 'date',
            'expected_return_percentage' => 'decimal:2',
            'actual_return_percentage' => 'decimal:2',
            'total_invested_amount' => 'decimal:2',
            'total_returned_amount' => 'decimal:2',
            'net_profit_loss' => 'decimal:2',
            'metadata' => 'json',
        ];
    }

    // Relationships
    public function investmentType(): BelongsTo
    {
        return $this->belongsTo(InvestmentType::class, 'investment_type_id');
    }

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'investor_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InvestmentTransaction::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(InvestmentStatusHistory::class)->orderByDesc('changed_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(InvestmentDocument::class);
    }

    public function accountingEntries(): HasMany
    {
        return $this->hasMany(InvestmentAccountingEntry::class);
    }

    public function performanceSnapshots(): HasMany
    {
        return $this->hasMany(InvestmentPerformanceSnapshot::class)->orderByDesc('snapshot_date');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMatured($query)
    {
        return $query->where('status', 'matured');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByType($query, $investmentTypeId)
    {
        return $query->where('investment_type_id', $investmentTypeId);
    }

    public function scopeByInvestor($query, $investorId)
    {
        return $query->where('investor_id', $investorId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('start_date', [$from, $to]);
    }

    public function scopeRecentlyModified($query, $days = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'suspended']);
    }

    // Business Logic Methods

    public function getTotalInvestedAmount(): float
    {
        return $this->transactions()
            ->where('status', 'processed')
            ->whereIn('transaction_type', ['INITIAL_INVESTMENT', 'ADDITIONAL_INVESTMENT'])
            ->sum('amount');
    }

    public function getTotalReturnedAmount(): float
    {
        return $this->transactions()
            ->where('status', 'processed')
            ->whereIn('transaction_type', ['PROFIT_DISTRIBUTION', 'DIVIDEND_PAYMENT', 'WITHDRAWAL'])
            ->sum('amount');
    }

    public function getNetProfitLoss(): float
    {
        $profits = $this->transactions()
            ->where('status', 'processed')
            ->whereIn('transaction_type', ['PROFIT_DISTRIBUTION', 'DIVIDEND_PAYMENT'])
            ->sum('amount');

        $losses = $this->transactions()
            ->where('status', 'processed')
            ->where('transaction_type', 'LOSS_ADJUSTMENT')
            ->sum('amount');

        return $profits - $losses;
    }

    public function getReturnPercentage(): float
    {
        $totalInvested = $this->getTotalInvestedAmount();

        if ($totalInvested == 0) {
            return 0;
        }

        $netProfit = $this->getNetProfitLoss();
        return ($netProfit / $totalInvested) * 100;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            'draft' => ['active', 'cancelled'],
            'active' => ['matured', 'closed', 'suspended'],
            'matured' => ['closed'],
            'closed' => [],
            'suspended' => ['active', 'closed'],
            'cancelled' => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }

    public function canWithdraw(): bool
    {
        return in_array($this->status, ['active', 'suspended']);
    }

    public function getRemainingTenureDays(): ?int
    {
        if (!$this->maturity_date) {
            return null;
        }

        $days = now()->diffInDays($this->maturity_date, false);
        return $days > 0 ? $days : 0;
    }

    public function isMaturityDue(): bool
    {
        return $this->maturity_date && now()->isAfter($this->maturity_date);
    }

    public function transitionStatus(string $newStatus, string $reason, ?string $notes = null): InvestmentStatusHistory
    {
        if (!$this->canTransitionTo($newStatus)) {
            throw new \Exception("Cannot transition from {$this->status} to {$newStatus}");
        }

        $oldStatus = $this->status;
        $this->update([
            'status' => $newStatus,
            'updated_by' => auth()->id(),
        ]);

        return InvestmentStatusHistory::create([
            'investment_id' => $this->id,
            'status_from' => $oldStatus,
            'status_to' => $newStatus,
            'reason' => $reason,
            'notes' => $notes,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);
    }

    public function recordTransaction(array $data): InvestmentTransaction
    {
        // Generate transaction number
        $year = now()->year;
        $sequence = $this->transactions()->whereYear('created_at', $year)->count() + 1;
        $transactionNumber = sprintf('TXN-%d-%06d', $year, $sequence);

        return $this->transactions()->create([
            'transaction_number' => $transactionNumber,
            'transaction_type' => $data['transaction_type'],
            'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
            'amount' => $data['amount'],
            'reference_number' => $data['reference_number'] ?? null,
            'description' => $data['description'],
            'status' => $data['status'] ?? 'processed',
            'metadata' => $data['metadata'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }
}
