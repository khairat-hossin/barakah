<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['member_id', 'allocated_shares_count', 'ownership_percentage', 'nominee_total_percentage', 'has_nominees', 'last_transfer_date'])]
class ShareAllocationHistory extends Model
{
    protected function casts(): array
    {
        return [
            'last_transfer_date' => 'date',
            'has_nominees' => 'boolean',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
