<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class FiscalYear extends Model
{
    protected $fillable = ['name'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function totalFolders(): int
    {
        return $this->documents()->distinct('government_level_id')->count('government_level_id')
             + $this->documents()->distinct('budget_type_id')->count('budget_type_id');
    }

    public function getFormattedStorageAttribute(): string
    {
        $bytes = $this->documents->reduce(fn($carry, $doc) =>
            $carry + ($doc->pdf_path && Storage::disk('public')->exists($doc->pdf_path)
                ? Storage::disk('public')->size($doc->pdf_path) : 0), 0);

        return $this->formatBytes($bytes);
    }

    public function totalFiles(): int
    {
        return $this->documents()->count();
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}
