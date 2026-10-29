<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return \App\Support\Bytes::format($this->documents()->sum('file_size'));
    }

    public function totalFiles(): int
    {
        return $this->documents()->count();
    }
}
