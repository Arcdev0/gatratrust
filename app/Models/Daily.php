<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Daily extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'plan_today',
        'plan_tomorrow',
        'problem',
        'upload_file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}