<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'fiscal_year_id',
        'government_level_id',
        'budget_type_id',
        'month_id',
        'day',
        'document_category_id',
        'file_number',
        'document_name',
        'pdf_path',
        'total_pages',
        'file_size',
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
        return \App\Support\Bytes::format($this->file_size);
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }
}
