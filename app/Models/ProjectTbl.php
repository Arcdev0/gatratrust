<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTbl extends Model
{
    use HasFactory;

    protected $table = 'projects'; // Explicitly define table name

    protected $fillable = [
        'nama_project',
        'no_project',
        'client_id',
        'kerjaan_id',
        'deskripsi',
        'total_biaya_project',
        'start',
        'end',
        'created_by'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'created_at',
        'updated_at'
    ];


    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function administrasiFiles()
    {
        return $this->hasMany(AdministrasiFile::class);
    }

    public function kerjaan()
    {
        return $this->belongsTo(Kerjaan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

        public function invoices()
    {
        return $this->hasMany(Invoice::class, 'project_id');
    }

    public function getTotalInvoiceAttribute()
    {
        return $this->invoices()->sum('net_total');
    }

    public function getSisaNominalAttribute()
    {
        return $this->total_biaya_project - $this->total_invoice;
    }

        public function getIsLunasAttribute()
    {
        return $this->total_invoice >= $this->total_biaya_project;
    }

    public function pics()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

}
