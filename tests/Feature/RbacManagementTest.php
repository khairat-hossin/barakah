<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RbacManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_user_without_manage_users_permission_cannot_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Member');

        $response = $this->actingAs($user)->get(route('user-management.users.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->get(route('user-management.users.index'));

        $response->assertOk();
    }

    public function test_user_cannot_delete_own_account(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->delete(route('user-management.users.destroy', $user));

        $response->assertRedirect(route('user-management.users.index'));
        $response->assertSessionHas('error', 'You cannot delete your own account.');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_last_super_admin_cannot_lose_super_admin_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');
        $memberRole = Role::findByName('Member', 'web');

        $response = $this->actingAs($user)->put(route('user-management.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => [$memberRole->id],
            'permissions' => [],
        ]);

        $response->assertRedirect(route('user-management.users.index'));
        $response->assertSessionHas('error', 'The last Super Admin cannot lose the Super Admin role.');
        $this->assertTrue($user->fresh()->hasRole('Super Admin'));
    }

    public function test_protected_permission_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');
        $permission = Permission::findByName('manage users', 'web');

        $response = $this->actingAs($user)->delete(route('user-management.permissions.destroy', $permission));

        $response->assertRedirect(route('user-management.permissions.index'));
        $response->assertSessionHas('error', 'System permissions cannot be deleted.');
        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }

    public function test_role_assigned_to_users_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $managedUser = User::factory()->create();
        $managedUser->assignRole('Treasurer');
        $role = Role::findByName('Treasurer', 'web');

        $response = $this->actingAs($user)->delete(route('user-management.roles.destroy', $role));

        $response->assertRedirect(route('user-management.roles.index'));
        $response->assertSessionHas('error', 'Remove this role from users before deleting it.');
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }
}
