<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'category',
    'status',
    'budget_requested',
    'budget_approved',
    'expected_return_percentage',
    'start_date',
    'deadline',
    'progress_percentage',
    'notes',
])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'budget_requested' => 'decimal:2',
            'budget_approved' => 'decimal:2',
            'expected_return_percentage' => 'decimal:2',
            'start_date' => 'date',
            'deadline' => 'date',
            'progress_percentage' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function leadInitials(): Attribute
    {
        return Attribute::get(function (): string {
            $name = trim((string) optional($this->owner)->name);

            if ($name === '') {
                return 'NA';
            }

            $parts = preg_split('/\s+/', $name) ?: [];
            $initials = collect($parts)
                ->filter()
                ->take(2)
                ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
                ->implode('');

            return $initials !== '' ? $initials : 'NA';
        });
    }
}
