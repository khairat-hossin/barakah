<?php

namespace App\Helpers;

use App\Models\Member;

class ProfileHelper
{
    public static function calculateCompleteness(Member $member): array
    {
        $parameters = [
            'personal_information' => self::isPersonalInfoComplete($member),
            'contact_information' => self::isContactInfoComplete($member),
            'nominee' => self::isNomineeComplete($member),
            'share_ownership' => self::hasShareOwnership($member),
            'documents' => self::hasDocuments($member),
        ];

        $completedCount = count(array_filter($parameters));
        $percentage = ($completedCount / 5) * 100;

        return [
            'percentage' => (int)$percentage,
            'status' => self::getStatus($percentage),
            'color' => self::getColor($percentage),
            'completed_parameters' => $completedCount,
            'total_parameters' => 5,
            'details' => $parameters,
            'missing_fields' => self::getMissingFields($member, $parameters),
        ];
    }

    private static function isPersonalInfoComplete(Member $member): bool
    {
        return !empty($member->date_of_birth)
            && !empty($member->address)
            && !empty($member->city)
            && !empty($member->postal_code);
    }

    private static function isContactInfoComplete(Member $member): bool
    {
        return !empty($member->email) && !empty($member->phone);
    }

    private static function isNomineeComplete(Member $member): bool
    {
        return !empty($member->nominee_name)
            && !empty($member->nominee_relation)
            && !empty($member->nominee_phone);
    }

    private static function hasShareOwnership(Member $member): bool
    {
        return $member->shares()->count() > 0;
    }

    private static function hasDocuments(Member $member): bool
    {
        return $member->documents()->count() > 0;
    }

    private static function getStatus(int $percentage): string
    {
        if ($percentage === 0) return 'Not Started';
        if ($percentage < 40) return 'Incomplete';
        if ($percentage < 60) return 'Partial';
        if ($percentage < 100) return 'Mostly Complete';
        return 'Complete';
    }

    private static function getColor(int $percentage): string
    {
        if ($percentage === 0) return 'secondary';
        if ($percentage < 40) return 'danger';
        if ($percentage < 60) return 'warning';
        if ($percentage < 100) return 'info';
        return 'success';
    }

    private static function getMissingFields(Member $member, array $parameters): array
    {
        $missing = [];

        if (!$parameters['personal_information']) {
            $fields = [];
            if (empty($member->date_of_birth)) $fields[] = 'Date of Birth';
            if (empty($member->address)) $fields[] = 'Address';
            if (empty($member->city)) $fields[] = 'City';
            if (empty($member->postal_code)) $fields[] = 'Postal Code';
            $missing['personal_information'] = $fields;
        }

        if (!$parameters['contact_information']) {
            $fields = [];
            if (empty($member->email)) $fields[] = 'Email';
            if (empty($member->phone)) $fields[] = 'Phone';
            $missing['contact_information'] = $fields;
        }

        if (!$parameters['nominee']) {
            $fields = [];
            if (empty($member->nominee_name)) $fields[] = 'Nominee Name';
            if (empty($member->nominee_relation)) $fields[] = 'Nominee Relation';
            if (empty($member->nominee_phone)) $fields[] = 'Nominee Phone';
            $missing['nominee'] = $fields;
        }

        if (!$parameters['share_ownership']) {
            $missing['share_ownership'] = ['No shares assigned'];
        }

        if (!$parameters['documents']) {
            $missing['documents'] = ['No documents uploaded'];
        }

        return $missing;
    }
}
