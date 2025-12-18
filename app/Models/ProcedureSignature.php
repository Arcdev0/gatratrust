<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcedureSignature extends Model
{
    public const ROLE_PREPARED_BY = 'prepared_by';
    public const ROLE_CHECKED_BY  = 'checked_by';
    public const ROLE_APPROVED_BY = 'approved_by';

    protected $fillable = [
        'procedure_revision_id',
        'role',
        'user_id',
        'signed_at',
        'note',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function revision(): BelongsTo
    {
        return $this->belongsTo(ProcedureRevision::class, 'procedure_revision_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
