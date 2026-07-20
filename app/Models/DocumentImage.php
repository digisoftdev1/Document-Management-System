<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentImage extends Model
{
    protected $fillable = [
        'document_id',
        'image_path',
        'page_number',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
