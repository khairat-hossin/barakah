<?php

namespace App\Http\Controllers;

use App\Support\RbacDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::query()
            ->withCount(['roles', 'users'])
            ->orderBy('name')
            ->get();

        return view('user-management.permissions.index', [
            'permissions' => $permissions,
            'protectedPermissions' => RbacDefaults::protectedPermissions(),
        ]);
    }

    public function create(): View
    {
        return view('user-management.permissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('user-management.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission): View
    {
        return view('user-management.permissions.edit', [
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,'.$permission->id],
        ]);

        if (
            in_array($permission->name, RbacDefaults::protectedPermissions(), true)
            && $validated['name'] !== $permission->name
        ) {
            return redirect()
                ->route('user-management.permissions.index')
                ->with('error', 'System permissions cannot be renamed.');
        }

        $permission->update([
            'name' => $validated['name'],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('user-management.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if (in_array($permission->name, RbacDefaults::protectedPermissions(), true)) {
            return redirect()
                ->route('user-management.permissions.index')
                ->with('error', 'System permissions cannot be deleted.');
        }

        if ($permission->roles()->exists() || $permission->users()->exists()) {
            return redirect()
                ->route('user-management.permissions.index')
                ->with('error', 'Remove this permission from roles and users before deleting it.');
        }

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('user-management.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
