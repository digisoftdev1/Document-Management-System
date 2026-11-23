<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
{
    private function authorizeManagement(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function index()
    {
        $this->authorizeManagement();

        $roles = Role::where('guard_name', 'web')
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorizeManagement();

        return view('roles.create');
    }

    public function store(Request $request)
    {
        $this->authorizeManagement();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
            ],
        ]);

        Role::create([
            'name' => strtolower($validated['name']),
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $this->authorizeManagement();

        abort_unless($role->guard_name === 'web', 404);

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($permission) => explode(' ', $permission->name, 2)[1] ?? 'Other');

        $assignedPermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact(
            'role',
            'permissions',
            'assignedPermissions'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorizeManagement();

        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'superadmin') {
            return back()->with('error', 'The superadmin role cannot be renamed.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('roles', 'name')
                    ->where('guard_name', 'web')
                    ->ignore($role->id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::exists('permissions', 'name')->where('guard_name', 'web'),
            ],
        ]);

        $role->name = strtolower($validated['name']);
        $role->save();

        $role->syncPermissions($validated['permissions'] ?? []);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role and permissions updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorizeManagement();

        abort_unless($role->guard_name === 'web', 404);

        if ($role->name === 'superadmin') {
            return back()->with('error', 'The superadmin role cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'This role is assigned to users. Remove it from users before deleting.');
        }

        $role->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}