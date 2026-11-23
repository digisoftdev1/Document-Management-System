<?php

namespace App\Models;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Contracts\Role as RoleContract;

class Role extends Model implements RoleContract
{
    use HasRoles;

    protected $fillable = ['name', 'guard_name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function findByName(BackedEnum|string $name, ?string $guardName): static
    {
        return static::where('name', $name)
            ->when($guardName, fn ($query) => $query->where('guard_name', $guardName))
            ->first();
    }

    public static function findById(int|string $id, ?string $guardName): static
    {
        return static::where('id', $id)
            ->when($guardName, fn ($query) => $query->where('guard_name', $guardName))
            ->first();
    }

    public static function findOrCreate(BackedEnum|string $name, ?string $guardName): static
    {
        $role = static::where('name', $name)
            ->when($guardName, fn ($query) => $query->where('guard_name', $guardName))
            ->first();

        if ($role) {
            return $role;
        }

        return static::create(['name' => (string) $name, 'guard_name' => $guardName]);
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class);
    }

    public function hasPermissionTo(BackedEnum|string|int|\Spatie\Permission\Contracts\Permission $permission, ?string $guardName = null): bool
    {
        return $this->permissions()->where('name', $permission)
            ->when($guardName, fn ($query) => $query->where('guard_name', $guardName))
            ->exists();
    }
}