<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\RbacDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with(['roles', 'permissions'])
            ->orderBy('name')
            ->get();

        $superAdminCount = User::role('Super Admin')->count();

        return view('user-management.users.index', [
            'users' => $users,
            'superAdminCount' => $superAdminCount,
        ]);
    }

    public function create(): View
    {
        return view('user-management.users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles(Role::whereIn('id', $validated['roles'] ?? [])->get());
        $user->syncPermissions(Permission::whereIn('id', $validated['permissions'] ?? [])->get());
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('user-management.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('user-management.users.edit', [
            'user' => $user->load(['roles', 'permissions']),
            'roles' => Role::query()->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if (! $this->canUpdateRoles($user, $validated['roles'] ?? [])) {
            return redirect()
                ->route('user-management.users.index')
                ->with('error', 'The last Super Admin cannot lose the Super Admin role.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles(Role::whereIn('id', $validated['roles'] ?? [])->get());
        $user->syncPermissions(Permission::whereIn('id', $validated['permissions'] ?? [])->get());
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('user-management.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, Request $request): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return redirect()
                ->route('user-management.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if ($this->isLastSuperAdmin($user)) {
            return redirect()
                ->route('user-management.users.index')
                ->with('error', 'The last Super Admin account cannot be deleted.');
        }

        $user->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('user-management.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * @param  list<int|string>  $roleIds
     */
    protected function canUpdateRoles(User $user, array $roleIds): bool
    {
        if (! $user->hasRole('Super Admin')) {
            return true;
        }

        if (User::role('Super Admin')->count() > 1) {
            return true;
        }

        $selectedRoleNames = Role::query()
            ->whereIn('id', $roleIds)
            ->pluck('name')
            ->all();

        return in_array('Super Admin', $selectedRoleNames, true);
    }

    protected function isLastSuperAdmin(User $user): bool
    {
        return $user->hasRole('Super Admin') && User::role('Super Admin')->count() === 1;
    }
}
