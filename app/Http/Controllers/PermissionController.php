<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionController extends Controller
{
    private function authorizeManagement(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function index()
    {
        $this->authorizeManagement();

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($permission) => explode(' ', $permission->name, 2)[1] ?? 'Other');

        return view('permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorizeManagement();

        $validated = $request->validate([
            'action' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_-]+$/',
            ],
            'module' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_-]+$/',
            ],
        ]);

        $name = strtolower($validated['action'] . ' ' . $validated['module']);

        Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    public function destroy(Permission $permission)
    {
        $this->authorizeManagement();

        abort_unless($permission->guard_name === 'web', 404);

        if ($permission->roles()->exists()) {
            return back()->with('error', 'This permission is assigned to roles. Remove it from those roles first.');
        }

        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}