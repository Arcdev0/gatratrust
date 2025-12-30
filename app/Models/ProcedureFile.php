<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcedureFile extends Model
{
    protected $fillable = [
        'procedure_revision_id',
        'file_path',
        'file_name',
        'file_ext',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    public function revision(): BelongsTo
    {
        return $this->belongsTo(ProcedureRevision::class, 'procedure_revision_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
