<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $fillable = [
        'user_id',
        'created_by',
        'jenis',
        'project_id',
        'kerjaan_id',
        'proses_id',
        'judul_umum',
        'deskripsi',
        'status',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at'  => 'date',
        'finished_at' => 'date',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        // kalau tabel kamu bukan 'projects', sesuaikan modelnya
        return $this->belongsTo(ProjectTbl::class, 'project_id');
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class, 'task_id');
    }

    public function latestLog()
    {
        return $this->hasOne(TaskLog::class, 'task_id')->latestOfMany('tanggal');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function prosesRel()
    {
        return $this->belongsTo(KerjaanListProses::class, 'proses_id');
    }
}
