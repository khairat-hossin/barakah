<?php

namespace Database\Seeders;

use App\Support\RbacDefaults;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = RbacDefaults::permissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roles = RbacDefaults::roles();

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($rolePermissions);
        }

        $firstUser = User::query()->oldest()->first();

        if ($firstUser && ! $firstUser->hasRole('Super Admin')) {
            $firstUser->assignRole('Super Admin');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
