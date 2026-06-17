<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'account_type',
        'normal_balance',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'account_id');
    }

    public function debitMappings(): HasMany
    {
        return $this->hasMany(AccountingEventMapping::class, 'debit_account_id');
    }

    public function creditMappings(): HasMany
    {
        return $this->hasMany(AccountingEventMapping::class, 'credit_account_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeAssets($query)
    {
        return $query->byType('ASSET');
    }

    public function scopeLiabilities($query)
    {
        return $query->byType('LIABILITY');
    }

    public function scopeEquity($query)
    {
        return $query->byType('EQUITY');
    }

    public function scopeIncome($query)
    {
        return $query->byType('INCOME');
    }

    public function scopeExpenses($query)
    {
        return $query->byType('EXPENSE');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('code', 'asc');
    }

    // Helper Methods
    public function isAsset(): bool
    {
        return $this->account_type === 'ASSET';
    }

    public function isLiability(): bool
    {
        return $this->account_type === 'LIABILITY';
    }

    public function isEquity(): bool
    {
        return $this->account_type === 'EQUITY';
    }

    public function isIncome(): bool
    {
        return $this->account_type === 'INCOME';
    }

    public function isExpense(): bool
    {
        return $this->account_type === 'EXPENSE';
    }

    public function isDebit(): bool
    {
        return $this->normal_balance === 'DEBIT';
    }

    public function isCredit(): bool
    {
        return $this->normal_balance === 'CREDIT';
    }

    public function getHierarchicalName(): string
    {
        if ($this->parent) {
            return $this->parent->getHierarchicalName() . ' > ' . $this->name;
        }

        return $this->name;
    }

    public function getBalance($fromDate = null, $toDate = null)
    {
        $query = $this->journalEntries()->whereNull('deleted_at');

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $debits = $query->sum('debit_amount') ?? 0;
        $credits = $query->sum('credit_amount') ?? 0;

        if ($this->isDebit()) {
            return $debits - $credits;
        }

        return $credits - $debits;
    }
}
