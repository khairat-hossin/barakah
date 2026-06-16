<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['member_id', 'share_id', 'ownership_start_date', 'ownership_end_date', 'transfer_reference', 'notes'])]
class MemberShareOwnership extends Model
{
    use SoftDeletes;

    protected $table = 'member_share_ownership';

    protected function casts(): array
    {
        return [
            'ownership_start_date' => 'date',
            'ownership_end_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function share(): BelongsTo
    {
        return $this->belongsTo(Share::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(ShareTransfer::class, 'transfer_reference');
    }

    public function scopeCurrent($query)
    {
        return $query->whereNull('ownership_end_date');
    }

    public function scopeForShare($query, $shareId)
    {
        return $query->where('share_id', $shareId)->orderByDesc('ownership_start_date');
    }
}
