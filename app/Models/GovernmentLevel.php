<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GovernmentLevel extends Model
{
    protected $fillable = ['name'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    private function scopeDocuments($query, $fyId)
    {
        return $query->where('government_level_id', $this->id)
            ->when($fyId, fn($q) => $q->where('fiscal_year_id', $fyId));
    }

    public function totalFolders($fyId = null): int
    {
        return $this->scopeDocuments(Document::query(), $fyId)->distinct('budget_type_id')->count('budget_type_id');
    }

    public function totalFiles($fyId = null): int
    {
        return $this->scopeDocuments(Document::query(), $fyId)->count();
    }

    public function formattedStorage($fyId = null): string
    {
        return \App\Support\Bytes::format(
            $this->scopeDocuments(Document::query(), $fyId)->sum('file_size')
        );
    }
}
