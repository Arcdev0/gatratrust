<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pak extends Model
{
    use HasFactory;

    protected $table = 'paks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pak_name',
        'pak_number',
        'pak_value',
        'location',
        'date',
        'po_amount',
        'pph_23',
        'ppn',
        'total_pak_cost',
        'estimated_profit',
        'total_cost_percentage',
        'created_by',
        // tambahkan kolom lain yang ingin diisi lewat create/update
    ];

    protected $casts = [
        'date' => 'date',
        'project_value' => 'decimal:2',
    ];

    /**
     * Relasi ke PakItem
     */
    public function items()
    {
        return $this->hasMany(PakItem::class, 'pak_id', 'id');
    }

    /**
     * Get employees as array
     */
    public function karyawans()
    {
        return $this->belongsToMany(KaryawanData::class, 'karyawan_pak', 'pak_id', 'karyawan_id')
            ->withTimestamps();
    }

    /**
     * Get employee names
     */
    public function getEmployeeNamesAttribute()
    {
        $employeeIds = json_decode($this->employee, true);

        if (!$employeeIds || !is_array($employeeIds)) {
            return [];
        }

        return KaryawanData::whereIn('id', $employeeIds)
            ->pluck('nama_lengkap')
            ->toArray();
    }

    public function projects()
    {
        return $this->hasMany(ProjectTbl::class, 'pak_id');
    }
}
