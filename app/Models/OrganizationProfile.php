<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'organization_name_bn', 'organization_name_en', 'short_name', 'logo_path', 'seal_path',
    'registration_number', 'registration_date', 'organization_type', 'status',
    'mobile_number', 'secondary_mobile', 'email', 'website', 'facebook_page', 'whatsapp_number',
    'address_line', 'village_area', 'post_office', 'union_ward', 'upazila', 'district', 'division', 'postal_code', 'country',
    'motto', 'vision_statement', 'mission_statement', 'core_values', 'objectives', 'about_organization',
    'total_shares', 'share_face_value', 'currency', 'share_ownership_model', 'share_transfer_allowed',
    'partial_share_transfer_allowed', 'minimum_shares_per_member', 'maximum_shares_per_member',
    'membership_type', 'new_member_admission_allowed', 'membership_approval_required', 'minimum_share_requirement',
    'maximum_share_ownership', 'allow_membership_transfer',
    'committee_term_length', 'maximum_consecutive_terms', 'election_required', 're_election_allowed', 'committee_positions',
    'default_currency', 'reserve_fund_percentage', 'emergency_fund_percentage',
    'bank_name', 'branch_name', 'account_name', 'account_number', 'routing_number', 'swift_code',
    'membership_fee', 'share_purchase_fee', 'annual_contribution', 'special_contribution',
    'general_meeting_notice_days', 'general_meeting_quorum_percentage', 'general_meeting_voting_method',
    'committee_meeting_notice_days', 'committee_meeting_quorum_percentage', 'minimum_committee_meetings_per_year',
    'created_by', 'updated_by'
])]
class OrganizationProfile extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'core_values' => 'array',
            'objectives' => 'array',
            'committee_positions' => 'array',
            'share_transfer_allowed' => 'boolean',
            'partial_share_transfer_allowed' => 'boolean',
            'new_member_admission_allowed' => 'boolean',
            'membership_approval_required' => 'boolean',
            'allow_membership_transfer' => 'boolean',
            'election_required' => 'boolean',
            're_election_allowed' => 'boolean',
            'share_face_value' => 'decimal:2',
            'reserve_fund_percentage' => 'decimal:2',
            'emergency_fund_percentage' => 'decimal:2',
            'membership_fee' => 'decimal:2',
            'share_purchase_fee' => 'decimal:2',
            'annual_contribution' => 'decimal:2',
            'special_contribution' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(OrganizationProfileAuditLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->organization_name_en ?? $this->organization_name_bn ?? 'Organization';
    }
}
