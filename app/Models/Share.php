<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['share_number', 'issue_date', 'status'])]
class Share extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
        ];
    }

    public function ownershipHistory(): HasMany
    {
        return $this->hasMany(MemberShareOwnership::class);
    }

    public function currentOwner()
    {
        return $this->hasOne(MemberShareOwnership::class)
            ->whereNull('ownership_end_date')
            ->with('member');
    }
}
