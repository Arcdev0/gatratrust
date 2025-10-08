<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false; // karena kita cuma pakai created_at
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'reference',
        'description',
        'created_at',
    ];
}
