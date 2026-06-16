<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDepositMonth extends Model
{
    protected $fillable = ['member_id', 'month', 'year', 'savings_entry_id'];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function savingsEntry(): BelongsTo
    {
        return $this->belongsTo(SavingsEntry::class);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function getMonthNameAttribute(): string
    {
        return \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }
}
