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
            'update members',
            'delete members',
            'view deposits',
            'create deposits',
            'view projects',
            'create projects',
            'view shares',
            'manage shares',
            'view share transfers',
            'create share transfers',
            'approve share transfers',
            'manage nominees',
            'manage executive committee',
            'manage documents',
            'view audit logs',
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
                'update members',
                'delete members',
                'view deposits',
                'create deposits',
                'view projects',
                'create projects',
                'view shares',
                'manage shares',
                'view share transfers',
                'create share transfers',
                'approve share transfers',
                'manage nominees',
                'manage executive committee',
                'manage documents',
                'view audit logs',
            ],
            'Treasurer' => [
                'view dashboard',
                'view members',
                'view deposits',
                'create deposits',
                'view projects',
                'view shares',
                'view share transfers',
                'manage documents',
            ],
            'Project Manager' => [
                'view dashboard',
                'view members',
                'view savings',
                'view projects',
                'create projects',
                'view shares',
            ],
            'Member' => [
                'view dashboard',
                'view shares',
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
