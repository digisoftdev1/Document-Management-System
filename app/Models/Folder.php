<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Folder extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'created_by',
    ];

    /**
     * Request-level parent cache.
     *
     * Prevents repeated queries such as:
     *
     * select * from folders where id = 1 limit 1
     *
     * from being executed over and over.
     */
    protected static array $parentCache = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id')
            ->orderByRaw("
                CASE
                    WHEN name REGEXP '^[0-9]+ '
                        THEN CAST(SUBSTRING_INDEX(name, ' ', 1) AS UNSIGNED)
                    ELSE 999
                END
            ")
            ->orderBy('name');
    }

    public function files(): HasMany
    {
        return $this->hasMany(RepositoryFile::class)
            ->orderBy('original_name');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get parent using a request-level cache.
     */
    protected function cachedParent(): ?Folder
    {
        if (!$this->parent_id) {
            return null;
        }

        $id = (int) $this->parent_id;

        if (array_key_exists($id, static::$parentCache)) {
            return static::$parentCache[$id];
        }

        return static::$parentCache[$id] = static::find($id);
    }

    /**
     * Build physical storage path.
     */
    public function getFullPathAttribute(): string
    {
        $segments = [$this->name];
        $folder = $this;

        while ($folder->parent_id) {
            $folder = $folder->cachedParent();

            if (!$folder) {
                break;
            }

            array_unshift($segments, $folder->name);
        }

        return 'repository/' . implode('/', $segments);
    }

    /**
     * Build human-readable repository path.
     */
    public function getRepositoryPathAttribute(): string
    {
        $segments = [$this->name];
        $folder = $this;

        while ($folder->parent_id) {
            $folder = $folder->cachedParent();

            if (!$folder) {
                break;
            }

            array_unshift($segments, $folder->name);
        }

        return 'Repository > ' . implode(' > ', $segments);
    }

    /**
     * Return ancestors from root → immediate parent.
     */
    public function ancestors()
    {
        $ancestors = collect();
        $folder = $this;

        while ($folder->parent_id) {
            $folder = $folder->cachedParent();

            if (!$folder) {
                break;
            }

            $ancestors->prepend($folder);
        }

        return $ancestors;
    }

    /**
     * Clear the request-level parent cache.
     */
    public static function clearParentCache(): void
    {
        static::$parentCache = [];
    }
}