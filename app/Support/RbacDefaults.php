<?php

namespace App\Support;

class RbacDefaults
{
    /**
     * @return list<string>
     */
    public static function permissions(): array
    {
        return [
            'view dashboard',
            'view members',
            'create members',
            'view savings',
            'create savings',
            'view projects',
            'create projects',
            'manage permissions',
            'manage roles',
            'manage users',
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function roles(): array
    {
        return [
            'Super Admin' => self::permissions(),
            'Association Admin' => [
                'view dashboard',
                'view members',
                'create members',
                'view savings',
                'create savings',
                'view projects',
                'create projects',
            ],
            'Treasurer' => [
                'view dashboard',
                'view members',
                'view savings',
                'create savings',
                'view projects',
            ],
            'Project Manager' => [
                'view dashboard',
                'view members',
                'view savings',
                'view projects',
                'create projects',
            ],
            'Member' => [
                'view dashboard',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function protectedPermissions(): array
    {
        return self::permissions();
    }

    /**
     * @return list<string>
     */
    public static function protectedRoles(): array
    {
        return ['Super Admin'];
    }
}
