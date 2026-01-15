<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coa extends Model
{
    protected $table = 'coa';

    // kalau kolom created_at / updated_at tetap standard Laravel, biarkan true
    // kalau migration kamu pakai timestamp default manual, tetap aman pakai timestamps = true
    public $timestamps = true;

    protected $fillable = [
        'code_account_id',
        'parent_id',
        'name',
        'description',
        'set_as_group',
        'default_posisi',
    ];

    protected $casts = [
        'set_as_group' => 'boolean',
    ];

    /** Parent COA (tree) */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'parent_id');
    }

    /** Children COA (tree) */
    public function children(): HasMany
    {
        return $this->hasMany(Coa::class, 'parent_id')->orderBy('code_account_id');
    }

    /** Lines jurnal yang pakai akun ini */
    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'coa_id');
    }
}
