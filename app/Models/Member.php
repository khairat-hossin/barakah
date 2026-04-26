<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'member_code',
    'name',
    'email',
    'phone',
    'join_date',
    'status',
    'monthly_saving_amount',
    'notes',
])]
class Member extends Model
{
    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'monthly_saving_amount' => 'decimal:2',
        ];
    }

    public function savingsEntries(): HasMany
    {
        return $this->hasMany(SavingsEntry::class);
    }

    protected function initials(): Attribute
    {
        return Attribute::get(function (): string {
            $name = trim($this->name);
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
