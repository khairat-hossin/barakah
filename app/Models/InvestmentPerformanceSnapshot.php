<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'investment_id',
    'snapshot_date',
    'total_invested',
    'current_value',
    'unrealized_gain_loss',
    'realized_gain_loss',
    'return_percentage',
    'transaction_count',
    'notes',
])]
class InvestmentPerformanceSnapshot extends Model
{
    public $timestamps = false;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'total_invested' => 'decimal:2',
            'current_value' => 'decimal:2',
            'unrealized_gain_loss' => 'decimal:2',
            'realized_gain_loss' => 'decimal:2',
            'return_percentage' => 'decimal:2',
        ];
    }

    // Relationships
    public function investment(): BelongsTo
    {
        return $this->belongsTo(Investment::class);
    }

    // Scopes
    public function scopeLatest($query)
    {
        return $query->orderByDesc('snapshot_date');
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('snapshot_date', [$from, $to]);
    }
}
