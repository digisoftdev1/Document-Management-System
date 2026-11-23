<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModulePermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $module
    ): Response {

        // Ensure the user is authenticated
        if (!$request->user()) {
            abort(401);
        }

        $action = $request->route()->getActionMethod();

        $permission = match ($action) {

            // View
            'index',
            'show',
            'records',
            'archivedview',
            'recordsview' => 'view',

            // Create
            'create',
            'store',
            'uploadFolder' => 'create',

            // Edit
            'edit',
            'update' => 'edit',

            // Delete
            'destroy',
            'bulkDestroy' => 'delete',

            default => null,
        };

        abort_unless(
            $permission !== null &&
            $request->user()->can("{$permission} {$module}"),
            403
        );

        return $next($request);
    }
}