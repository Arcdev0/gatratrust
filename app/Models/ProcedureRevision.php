<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProcedureRevision extends Model
{
    protected $fillable = [
        'procedure_id',
        'rev_no',
        'tanggal_rev',
        'change_note',
        'status',
        'reject_reason',
        'rejected_by',
        'rejected_at',
        'created_by',
    ];

    protected $casts = [
        'tanggal_rev'  => 'date',
        'rejected_at'  => 'datetime',
    ];

    // Status constants biar konsisten (hindari typo)
    public const STATUS_PENDING            = 'pending';
    public const STATUS_PENDING_CHECKED_BY = 'pending_checked_by';
    public const STATUS_PENDING_APPROVED_BY= 'pending_approved_by';
    public const STATUS_APPROVED           = 'approved';
    public const STATUS_REJECTED           = 'rejected';

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProcedureFile::class, 'procedure_revision_id');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(ProcedureSignature::class, 'procedure_revision_id');
    }

    public function preparedBySignature(): HasOne
    {
        return $this->hasOne(ProcedureSignature::class, 'procedure_revision_id')
            ->where('role', ProcedureSignature::ROLE_PREPARED_BY);
    }

    public function checkedBySignature(): HasOne
    {
        return $this->hasOne(ProcedureSignature::class, 'procedure_revision_id')
            ->where('role', ProcedureSignature::ROLE_CHECKED_BY);
    }

    public function approvedBySignature(): HasOne
    {
        return $this->hasOne(ProcedureSignature::class, 'procedure_revision_id')
            ->where('role', ProcedureSignature::ROLE_APPROVED_BY);
    }

    // Helper: format rev jadi "00", "01" dst
    public function revLabel(): string
    {
        return str_pad((string) $this->rev_no, 2, '0', STR_PAD_LEFT);
    }
}
