<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskLog extends Model
{
    use HasFactory;

    protected $table = 'task_logs';

    protected $fillable = [
        'daily_id',
        'task_id',
        'user_id',
        'tanggal',
        'keterangan',
        'status_hari_ini',
        'upload_file',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relations
    public function daily()
    {
        return $this->belongsTo(NewDaily::class, 'daily_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
