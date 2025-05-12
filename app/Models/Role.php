<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name'];

    /**
     * Relasi ke User (1 Role memiliki banyak User)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

