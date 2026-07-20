<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'fiscal_year_id',
        'government_level_id',
        'budget_type_id',
        'month_id',
        'day',
        'file_number',
        'document_name',
        'pdf_path',
        'total_pages',
        'created_by',
    ];

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function governmentLevel(): BelongsTo
    {
        return $this->belongsTo(GovernmentLevel::class);
    }

    public function budgetType(): BelongsTo
    {
        return $this->belongsTo(BudgetType::class);
    }

    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(DocumentImage::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedSizeAttribute(): string
    {
        if (!$this->pdf_path || !Storage::disk('public')->exists($this->pdf_path)) {
            return '0 KB';
        }

        $bytes = Storage::disk('public')->size($this->pdf_path);

        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
