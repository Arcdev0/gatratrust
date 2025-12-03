<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kerjaan extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kerjaan'];


    public function prosesList()
    {
        return $this->belongsToMany(ListProses::class, 'kerjaan_list_proses')
            ->withPivot('urutan', 'hari')
            ->orderBy('kerjaan_list_proses.urutan');
    }

    public function dailyItems()
    {
        return $this->hasMany(DailyItem::class, 'kerjaan_id');
    }
}
