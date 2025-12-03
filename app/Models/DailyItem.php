<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyItem extends Model
{
    protected $fillable = [
        'daily_id',
        'jenis',
        'project_id',
        'kerjaan_id',
        'proses_id',
        'pekerjaan_umum',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function daily()
    {
        return $this->belongsTo(Daily::class, 'daily_id');
    }

    public function project()
    {
        return $this->belongsTo(ProjectTbl::class, 'project_id');
    }

    public function kerjaan()
    {
        return $this->belongsTo(Kerjaan::class, 'kerjaan_id');
    }
    public function proses()
    {
        return $this->belongsTo(ListProses::class, 'proses_id');
    }
}
