<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RepositoryFile extends Model
{
    protected $fillable = [
        'folder_id',
        'original_name',
        'stored_name',
        'extension',
        'mime_type',
        'size',
        'disk',
        'path',
        'uploaded_by',
        'fiscal_year_id',
    ];

    protected $appends = [
        'url',
        'formatted_size',
    ];



    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }


    public function isPdf()
    {
        return $this->extension === 'pdf';
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Find the first filename free within a folder, judging by DB rows.
     *
     * The database is the source of truth: physical leftovers without a row
     * (crashed imports, truncated tables) get overwritten instead of forcing
     * spurious "(1)" suffixes.
     */
    public static function nextAvailableName(?int $folderId, string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $taken = function (string $candidate) use ($folderId): bool {
            return self::query()
                ->where(function ($q) use ($folderId) {
                    $folderId ? $q->where('folder_id', $folderId) : $q->whereNull('folder_id');
                })
                ->where(function ($q) use ($candidate) {
                    $q->where('original_name', $candidate)
                        ->orWhere('stored_name', $candidate);
                })
                ->exists();
        };

        if (!$taken($filename)) {
            return $filename;
        }

        $counter = 1;

        do {
            $candidate = "{$name} ({$counter}).{$ext}";
            $counter++;
        } while ($taken($candidate));

        return $candidate;
    }

    public function getRepositoryPathAttribute(): string
    {
        if (!$this->folder) {
            return 'Repository';
        }

        return $this->folder->repository_path;
    }
}
