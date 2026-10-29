<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return \App\Support\Bytes::format(
            $this->scopeDocuments(Document::query(), $fyId, $govId)->sum('file_size')
        );
    }
}
