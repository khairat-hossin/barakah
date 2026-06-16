<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['member_id', 'position', 'start_date', 'end_date', 'status'])]
class ExecutiveCommittee extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public const POSITIONS = [
        'president' => 'President',
        'senior_vice_president' => 'Senior Vice President',
        'vice_president' => 'Vice President',
        'general_secretary' => 'General Secretary',
        'joint_general_secretary' => 'Joint General Secretary',
        'treasurer' => 'Treasurer',
        'auditor' => 'Auditor',
        'organizing_secretary' => 'Organizing Secretary',
        'publicity_secretary' => 'Publicity Secretary',
        'office_secretary' => 'Office Secretary',
        'executive_member' => 'Executive Member',
    ];

    public const EXCLUSIVE_POSITIONS = [
        'president', 'senior_vice_president', 'vice_president',
        'general_secretary', 'joint_general_secretary', 'treasurer',
        'auditor', 'organizing_secretary', 'publicity_secretary', 'office_secretary',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeCurrent($query)
    {
        return $query->whereNull('end_date');
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position)->current();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->current();
    }
}
