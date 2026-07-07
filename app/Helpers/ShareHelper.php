<?php

namespace App\Helpers;

use App\Models\Member;
use App\Models\OrganizationProfile;

class ShareHelper
{
    /**
     * Outstanding deposit for a member, accrued monthly from the organisation's
     * global deposit-collection start month (same start for everyone, regardless
     * of when they joined).
     *
     * @return array{configured:bool, emi:float, expected_months:int, paid_months:int, due_months:int, due_amount:float, start:?\Illuminate\Support\Carbon}
     */
    public static function calculateDepositDue($memberId): array
    {
        $member = Member::find($memberId);
        $emi = (float) self::calculateEmiPerMonth($memberId);
        $paidMonths = $member ? $member->depositMonths()->count() : 0;
        $start = OrganizationProfile::first()?->deposit_start_month;

        $base = [
            'configured' => (bool) $start,
            'emi' => $emi,
            'expected_months' => 0,
            'paid_months' => $paidMonths,
            'due_months' => 0,
            'due_amount' => 0.0,
            'start' => $start,
        ];

        if (! $member || ! $start) {
            return $base;
        }

        $start = $start->copy()->startOfMonth();
        $now = now()->startOfMonth();

        // Inclusive count of months from the start month up to the current month.
        $expectedMonths = $start->greaterThan($now) ? 0 : $start->diffInMonths($now) + 1;
        $dueMonths = max(0, $expectedMonths - $paidMonths);

        return array_merge($base, [
            'expected_months' => $expectedMonths,
            'due_months' => $dueMonths,
            'due_amount' => $dueMonths * $emi,
        ]);
    }

    public static function calculateEmiPerMonth($memberId)
    {
        $member = Member::find($memberId);
        if (!$member) {
            return 0;
        }

        $shareCount = $member->shares()
            ->current()
            ->count();

        $orgProfile = OrganizationProfile::first();
        $shareFaceValue = $orgProfile?->share_face_value ?? 0;

        return $shareCount * $shareFaceValue;
    }
}
