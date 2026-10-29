<?php

namespace App\Models;

use App\Helpers\NepaliHelper;
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
        'fiscal_year_id',
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
    protected static ?array $fiscalYearMap = null;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }


    public static function normalizeFiscalYear(string $name): string
    {
        $name = strtr($name, [
            '०' => '0',
            '१' => '1',
            '२' => '2',
            '३' => '3',
            '४' => '4',
            '५' => '5',
            '६' => '6',
            '७' => '7',
            '८' => '8',
            '९' => '9',
        ]);

        $name = trim($name);

        // Normalize separators and surrounding whitespace only.
        $name = preg_replace('/\s*[-\/]\s*/u', '-', $name);

        return $name;
    }

    public static function resolveFiscalYearId(
        ?int $parentId,
        string $name
    ): ?int {
        if (static::$fiscalYearMap === null) {
            $years = FiscalYear::query()->get(['id', 'name']);

            $grouped = $years->groupBy(
                fn($year) => static::normalizeFiscalYear($year->name)
            );

            // Avoid arbitrarily choosing an ID if multiple records normalize
            // to the same fiscal year.
            static::$fiscalYearMap = $grouped
                ->filter(fn($matches) => $matches->count() === 1)
                ->map(fn($matches) => (int) $matches->first()->id)
                ->all();
        }

        $normalizedName = static::normalizeFiscalYear($name);

        if (isset(static::$fiscalYearMap[$normalizedName])) {
            return static::$fiscalYearMap[$normalizedName];
        }

        if ($parentId !== null) {
            return static::query()
                ->whereKey($parentId)
                ->value('fiscal_year_id');
        }

        return null;
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

    /**
     * Apply signed stat deltas to a folder and all of its ancestors.
     *
     * Keeps total_folders / total_files / total_storage (subtree aggregates)
     * up to date in O(depth) indexed UPDATEs instead of recomputing per read.
     */
    public static function shiftStats(
        ?int $folderId,
        int $folders = 0,
        int $files = 0,
        int $bytes = 0,
    ): void {
        if (!$folderId || ($folders === 0 && $files === 0 && $bytes === 0)) {
            return;
        }

        $current = $folderId;

        while ($current) {

            $query = static::query()->whereKey($current);

            if ($folders !== 0) {
                $query->increment('total_folders', $folders);
            }
            if ($files !== 0) {
                $query->increment('total_files', $files);
            }
            if ($bytes !== 0) {
                $query->increment('total_storage', $bytes);
            }

            $current = static::query()
                ->select('parent_id')
                ->find($current)
                    ?->parent_id;
        }
    }

    /**
     * Recompute subtree stats for every folder from scratch.
     *
     * Used by the migration backfill and `repository:recount` to repair drift.
     * Never call per-request.
     */
    public static function recountAll(): void
    {
        $allFolders = static::query()->select(['id', 'parent_id'])->get();

        $children = [];

        foreach ($allFolders as $folder) {
            $children[$folder->parent_id ?? 0][] = $folder->id;
        }

        $fileStats = \App\Models\RepositoryFile::query()
            ->selectRaw('folder_id, COUNT(*) as file_count, COALESCE(SUM(size), 0) as storage')
            ->whereNotNull('folder_id')
            ->groupBy('folder_id')
            ->get()
            ->keyBy('folder_id');

        $directFiles = [];
        $directStorage = [];

        foreach ($fileStats as $folderId => $stats) {
            $directFiles[$folderId] = (int) $stats->file_count;
            $directStorage[$folderId] = (int) $stats->storage;
        }

        $memo = [];

        $calculate = function (int $folderId) use (&$calculate, &$memo, &$children, &$directFiles, &$directStorage): array {

            if (isset($memo[$folderId])) {
                return $memo[$folderId];
            }

            $folders = 0;
            $files = $directFiles[$folderId] ?? 0;
            $storage = $directStorage[$folderId] ?? 0;

            foreach ($children[$folderId] ?? [] as $childId) {
                $child = $calculate($childId);

                $folders += 1 + $child['folders'];
                $files += $child['files'];
                $storage += $child['storage'];
            }

            return $memo[$folderId] = [
                'folders' => $folders,
                'files' => $files,
                'storage' => $storage,
            ];
        };

        foreach ($allFolders as $folder) {
            $stats = $calculate($folder->id);

            static::query()->whereKey($folder->id)->update([
                'total_folders' => $stats['folders'],
                'total_files' => $stats['files'],
                'total_storage' => $stats['storage'],
            ]);
        }
    }
}