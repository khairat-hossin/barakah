<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingEvent extends Model
{
    protected $fillable = [
        'event_code',
        'event_name',
        'event_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function mappings(): HasMany
    {
        return $this->hasMany(AccountingEventMapping::class, 'event_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('event_code', 'asc');
    }

    // Helper Methods
    public function isDeposit(): bool
    {
        return $this->event_type === 'DEPOSIT';
    }

    public function isExpense(): bool
    {
        return $this->event_type === 'EXPENSE';
    }

    public function isInvestment(): bool
    {
        return $this->event_type === 'INVESTMENT';
    }

    public function isShare(): bool
    {
        return $this->event_type === 'SHARE';
    }
}
