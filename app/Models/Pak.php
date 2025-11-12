<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pak extends Model
{
    protected $table = 'paks';
    protected $primaryKey = 'pak_id';
    
    protected $fillable = [
        'project_name',
        'project_number',
        'project_value',
        'location_project',
        'date',
        'employee'
    ];

    protected $casts = [
        'date' => 'date',
        'project_value' => 'decimal:2'
    ];

    public function items()
    {
        return $this->hasMany(PakItem::class, 'pak_id', 'pak_id');
    }
}