<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Month extends Model
{
    protected $fillable = [
        'name',
        'order_no',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
    public function getNameAttribute()
    {
        return app()->getLocale() === 'ne'
            ? $this->name_np
            : $this->name_en;
    }
}
