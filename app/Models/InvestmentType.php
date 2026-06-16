<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'code',
    'name',
    'description',
    'category',
    'default_tenure_months',
    'default_return_type',
    'requires_approval',
    'max_investment_amount',
    'min_investment_amount',
    'features',
    'is_active',
    'created_by',
    'updated_by',
])]
class InvestmentType extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'features' => 'json',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
            'max_investment_amount' => 'decimal:2',
            'min_investment_amount' => 'decimal:2',
        ];
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class, 'investment_type_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequiringApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function supportsFeature(string $feature): bool
    {
        if (!$this->features) {
            return false;
        }

        return in_array($feature, $this->features);
    }
}
