<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $appends = [
        'url',
        'formatted_size',
    ];



    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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

    public function getRepositoryPathAttribute(): string
    {
        if (!$this->folder) {
            return 'Repository';
        }

        return $this->folder->repository_path;
    }
}
