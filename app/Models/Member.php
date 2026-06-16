<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'member_code', 'name', 'name_bn', 'father_name', 'mother_name', 'spouse_name',
    'date_of_birth', 'gender', 'marital_status', 'nationality',
    'nid_number', 'birth_registration', 'passport_number', 'tax_id',
    'email', 'phone', 'secondary_mobile', 'whatsapp_number',
    'present_address_village', 'present_address_po', 'present_address_union',
    'present_address_upazila', 'present_address_district', 'present_address_postal',
    'same_as_permanent', 'permanent_address_village', 'permanent_address_po',
    'permanent_address_union', 'permanent_address_upazila', 'permanent_address_district',
    'permanent_address_postal', 'occupation', 'business_name', 'trade_license_number',
    'office_designation', 'employer_name', 'office_address',
    'photo_path', 'signature_path',
    'join_date', 'status', 'monthly_saving_amount', 'notes',
])]
class Member extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'date_of_birth' => 'date',
            'same_as_permanent' => 'boolean',
            'monthly_saving_amount' => 'decimal:2',
        ];
    }

    public function savingsEntries(): HasMany
    {
        return $this->hasMany(SavingsEntry::class);
    }

    public function depositMonths(): HasMany
    {
        return $this->hasMany(MemberDepositMonth::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(MemberShareOwnership::class)->whereNull('ownership_end_date');
    }

    public function shareOwnershipHistory(): HasMany
    {
        return $this->hasMany(MemberShareOwnership::class);
    }

    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class);
    }

    public function executiveCommittee(): HasMany
    {
        return $this->hasMany(ExecutiveCommittee::class);
    }

    public function currentPosition(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ExecutiveCommittee::class)->whereNull('end_date');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function shareTransfersFrom(): HasMany
    {
        return $this->hasMany(ShareTransfer::class, 'from_member_id');
    }

    public function shareTransfersTo(): HasMany
    {
        return $this->hasMany(ShareTransfer::class, 'to_member_id');
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

    public function totalSharesOwned(): int
    {
        return $this->shares()->count();
    }

    public function nomineeAllocationPercentage(): int
    {
        return $this->nominees()->sum('allocation_percentage');
    }

    public function hasCompleteProfile(): bool
    {
        return $this->name_bn
            && $this->father_name
            && $this->mother_name
            && $this->gender
            && $this->present_address_village
            && $this->present_address_po
            && $this->present_address_upazila
            && $this->present_address_district;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWithCompleteProfile($query)
    {
        return $query->whereNotNull('name_bn')
            ->whereNotNull('father_name')
            ->whereNotNull('mother_name')
            ->whereNotNull('gender');
    }
}
