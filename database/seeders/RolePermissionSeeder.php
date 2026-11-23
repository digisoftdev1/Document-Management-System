<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = [
            'dashboard' => ['view'],

            'users' => ['view', 'create', 'edit', 'delete'],

            'roles' => ['view', 'create', 'edit', 'delete'],

            'permissions' => ['view', 'create', 'edit', 'delete'],

            'documents' => ['view', 'create', 'edit', 'delete'],

            'repository' => ['view', 'create', 'edit', 'delete'],

            'folders' => ['view', 'create', 'edit', 'delete'],

            'fiscal-years' => ['view', 'create', 'edit', 'delete'],

            'government-levels' => ['view', 'create', 'edit', 'delete'],

            'budget-types' => ['view', 'create', 'edit', 'delete'],

            'document-categories' => ['view', 'create', 'edit', 'delete'],

            'reports' => ['view', 'export'],

            'settings' => ['view', 'edit'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action} {$module}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $superadmin = Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $editor = Role::firstOrCreate([
            'name' => 'editor',
            'guard_name' => 'web',
        ]);

        $viewer = Role::firstOrCreate([
            'name' => 'viewer',
            'guard_name' => 'web',
        ]);

        // Superadmin gets everything
        $superadmin->syncPermissions(Permission::all());

        // Admin gets everything except role/permission administration
        $adminPermissions = Permission::whereNotIn('name', [
            'create roles',
            'edit roles',
            'delete roles',
            'create permissions',
            'edit permissions',
            'delete permissions',
        ])->get();

        $admin->syncPermissions($adminPermissions);

        // Editor
        $editor->syncPermissions([
            'view dashboard',
            'view documents',
            'create documents',
            'edit documents',
            'view repository',
            'create repository',
            'view folders',
        ]);

        // Viewer
        $viewer->syncPermissions([
            'view dashboard',
            'view documents',
            'view repository',
            'view folders',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}