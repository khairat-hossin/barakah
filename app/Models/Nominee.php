<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['member_id', 'full_name', 'father_name', 'mother_name', 'date_of_birth', 'nid_number', 'birth_registration', 'relationship', 'mobile_number', 'email', 'address', 'allocation_percentage', 'is_primary'])]
class Nominee extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_primary' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId)->orderBy('is_primary', 'desc');
    }
}
