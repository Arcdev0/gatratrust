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
        'project_name',
        'project_number',
        'project_value',
        'location_project',
        'date',
        'employee',
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
    public function getEmployeesAttribute()
    {
        return json_decode($this->employee, true) ?? [];
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
}