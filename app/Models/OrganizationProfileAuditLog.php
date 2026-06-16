<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'organization_profile_id', 'changed_by', 'action_type', 'section_name',
    'field_name', 'old_value', 'new_value', 'notes', 'ip_address', 'user_agent', 'timestamp'
])]
class OrganizationProfileAuditLog extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
            'timestamp' => 'datetime',
        ];
    }

    public function organizationProfile(): BelongsTo
    {
        return $this->belongsTo(OrganizationProfile::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
