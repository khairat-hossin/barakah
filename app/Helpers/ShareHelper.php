<?php

namespace App\Helpers;

use App\Models\Member;
use App\Models\OrganizationProfile;

class ShareHelper
{
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
