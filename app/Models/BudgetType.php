<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BudgetType extends Model
{
    protected $fillable = ['name'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function totalFolders(): int
    {
        return 0;
    }

    private function scopeDocuments($query, $fyId, $govId)
    {
        return $query->where('budget_type_id', $this->id)
            ->when($fyId, fn($q) => $q->where('fiscal_year_id', $fyId))
            ->when($govId, fn($q) => $q->where('government_level_id', $govId));
    }

    public function totalFiles($fyId = null, $govId = null): int
    {
        return $this->scopeDocuments(Document::query(), $fyId, $govId)->count();
    }

    public function formattedStorage($fyId = null, $govId = null): string
    {
        $bytes = $this->scopeDocuments(Document::query(), $fyId, $govId)
            ->get(['pdf_path'])
            ->reduce(fn($carry, $doc) => $carry + ($doc->pdf_path && Storage::disk('public')->exists($doc->pdf_path)
                ? Storage::disk('public')->size($doc->pdf_path) : 0), 0);

        return $this->formatBytes($bytes);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }
}
