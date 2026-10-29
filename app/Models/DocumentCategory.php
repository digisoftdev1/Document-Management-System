<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    protected $fillable = [
        'name_en',
        'name_np',
    ];
    public function documents()
    {
        return $this->hasMany(\App\Models\Document::class);
    }
}