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
            'view investments',
            'create investments',
            'update investments',
            'delete investments',
            'manage investments',
            'view investment dashboard',
            'create investment transactions',
            'approve investment transactions',
            'manage investment transactions',
            'manage investment documents',
            'verify investment documents',
            'delete investment documents',
            'manage investment types',
            'view investment analytics',
            'view shares',
            'manage shares',
            'view share transfers',
            'create share transfers',
            'approve share transfers',
            'manage nominees',
            'manage executive committee',
            'manage documents',
            'manage organization profile',
            'view expenses',
            'create expenses',
            'update expenses',
            'delete expenses',
            'approve expenses',
            'manage expenses',
            'view loans',
            'create loans',
            'update loans',
            'delete loans',
            'approve loans',
            'manage loans',
            'view audit logs',
            'manage permissions',
            'manage roles',
            'manage users',
            'view accounting',
            'manage accounting',
            'create accounting entries',
            'update accounting entries',
            'delete accounting entries',
            'post accounting entries',
            'reverse accounting entries',
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
                'view investments',
                'create investments',
                'update investments',
                'delete investments',
                'manage investments',
                'view investment dashboard',
                'create investment transactions',
                'approve investment transactions',
                'manage investment transactions',
                'manage investment documents',
                'verify investment documents',
                'delete investment documents',
                'manage investment types',
                'view investment analytics',
                'view shares',
                'manage shares',
                'view share transfers',
                'create share transfers',
                'approve share transfers',
                'manage nominees',
                'manage executive committee',
                'manage documents',
                'manage organization profile',
                'view expenses',
                'create expenses',
                'update expenses',
                'delete expenses',
                'approve expenses',
                'manage expenses',
                'view loans',
                'create loans',
                'update loans',
                'delete loans',
                'approve loans',
                'manage loans',
                'view audit logs',
                'view accounting',
                'manage accounting',
                'create accounting entries',
                'update accounting entries',
                'post accounting entries',
            ],
            'Treasurer' => [
                'view dashboard',
                'view members',
                'view deposits',
                'create deposits',
                'view investments',
                'create investments',
                'view investment dashboard',
                'view investment analytics',
                'view shares',
                'view share transfers',
                'view expenses',
                'create expenses',
                'view loans',
                'create loans',
                'manage documents',
                'view accounting',
            ],
            'Project Manager' => [
                'view dashboard',
                'view members',
                'view deposits',
                'view investments',
                'create investments',
                'view investment dashboard',
                'create investment transactions',
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
