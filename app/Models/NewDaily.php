<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewDaily extends Model
{
    use HasFactory;

    protected $table = 'new_dailies';

    protected $fillable = [
        'user_id',
        'tanggal',
        'problem',
        'summary',
        'upload_file',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taskLogs()
    {
        return $this->hasMany(TaskLog::class, 'daily_id');
    }

    /**
     * Scope: filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: filter by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }
}
