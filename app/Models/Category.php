<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'code', // A, B, C, dll 
        'max_percentage',
        'order',
        'name',
        'description',
    ];

    public function pakItems(): HasMany
    {
        return $this->hasMany(PakItem::class, 'category_id');
    }
}
