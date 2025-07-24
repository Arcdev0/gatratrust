<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyComment extends Model
{
    protected $fillable = ['daily_id', 'user_id', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
