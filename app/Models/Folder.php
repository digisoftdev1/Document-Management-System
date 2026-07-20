<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Folder extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'description', 'created_by'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id')->orderBy('name');
    }

    public function files(): HasMany
    {
        return $this->hasMany(RepositoryFile::class)->orderBy('original_name');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        return 'repository/' . implode('/', $path);
    }

    public function totalFolders(): int
    {
        return $this->children->sum(fn($child) => 1 + $child->totalFolders());
    }

    public function totalFiles(): int
    {
        return $this->files()->count() + $this->children->sum->totalFiles();
    }

    public function totalStorage(): int
    {
        return $this->files()->sum('size') + $this->children->sum->totalStorage();
    }

    public function getRepositoryPathAttribute(): string
    {
        $segments = [$this->name];
        $parent = $this->parent;
        while ($parent) {
            array_unshift($segments, $parent->name);
            $parent = $parent->parent;
        }
        return 'Repository > ' . implode(' > ', $segments);
    }

    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }
        return $ancestors;
    }
}
