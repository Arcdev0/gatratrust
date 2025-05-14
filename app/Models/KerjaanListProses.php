<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KerjaanListProses extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kerjaan_list_proses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kerjaan_id',
        'list_proses_id',
        'urutan'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the kerjaan that owns the KerjaanListProses
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kerjaan(): BelongsTo
    {
        return $this->belongsTo(Kerjaan::class);
    }

    /**
     * Get the listProses that owns the KerjaanListProses
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listProses(): BelongsTo
    {
        return $this->belongsTo(ListProses::class);
    }
}
