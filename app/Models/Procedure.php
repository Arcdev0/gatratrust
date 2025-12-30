<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Procedure extends Model
{
    protected $fillable = [
        'no_dok',
        'nama_dok',
        'tanggal_berlaku',
        'created_by',
        'current_revision_id',
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(ProcedureRevision::class);
    }

    // revisi terbaru berdasarkan rev_no (paling aman)
    public function latestRevision(): HasOne
    {
        return $this->hasOne(ProcedureRevision::class)->latestOfMany('rev_no');
    }

    // pointer revisi aktif (kalau kamu pakai current_revision_id)
    public function currentRevision(): BelongsTo
    {
        return $this->belongsTo(ProcedureRevision::class, 'current_revision_id');
    }
}
