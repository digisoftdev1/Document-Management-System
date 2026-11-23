<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function availableRoles()
    {
        $query = Role::where('guard_name', 'web');

        if (!Auth::user()->hasRole('superadmin')) {
            $query->where('name', '!=', 'superadmin');
        }

        return $query->orderBy('name')->get();
    }

    private function roleRules(): array
    {
        $rule = Rule::exists('roles', 'id')
            ->where(function ($query) {
                $query->where('guard_name', 'web');

                if (!Auth::user()->hasRole('superadmin')) {
                    $query->where('name', '!=', 'superadmin');
                }
            });

        return [
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', $rule],
        ];
    }

    public function index()
    {
        $users = User::with('roles')->latest()->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->availableRoles()->sortBy('id');


        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->roleRules()));

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $roleIds = $validated['roles'] ?? [];

        $roles = Role::whereIn('id', $roleIds)
            ->where('guard_name', 'web')
            ->get();

        $user->syncRoles($roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);

        if ($user->hasRole('superadmin') && !Auth::user()->hasRole('superadmin')) {
            abort(403);
        }

        $roles = $this->availableRoles();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::with('roles')->findOrFail($id);

        $isSuperAdmin = Auth::user()->hasRole('superadmin');

        if ($user->hasRole('superadmin') && !$isSuperAdmin) {
            abort(403);
        }

        // Prevent a superadmin from removing their own superadmin access.
        if (
            $user->id === Auth::id()
            && $user->hasRole('superadmin')
            && !in_array(
                Role::where('name', 'superadmin')
                    ->where('guard_name', 'web')
                    ->value('id'),
                $request->input('roles', []),
                false
            )
        ) {
            return back()
                ->withInput()
                ->with('error', 'You cannot remove your own superadmin role.');
        }

        $validated = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], $this->roleRules()));

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $roleIds = $validated['roles'] ?? [];

        $roles = Role::whereIn('id', $roleIds)
            ->where('guard_name', 'web')
            ->get();

        $user->syncRoles($roles);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'You cannot delete a superadmin account.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}