<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FpuLine extends Model
{
    protected $table = 'fpu_lines';

    protected $fillable = [
        'fpu_id',
        'line_no',
        'description',
        'amount',
        'proof_count',
        'has_proof',

        // ✅ NEW
        'paid_journal_id',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'has_proof' => 'boolean',
        'proof_count' => 'integer',

        // ✅ NEW
        'paid_at' => 'datetime',
    ];

    public function fpu(): BelongsTo
    {
        return $this->belongsTo(Fpu::class, 'fpu_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(FpuLineAttachment::class, 'fpu_line_id');
    }

    // ✅ NEW: relation to paid journal (cash-out journal per line)
    public function paidJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'paid_journal_id');
    }

    /**
     * Refresh proof_count + has_proof based on attachments count.
     * (paid status FPU sekarang ditentukan dari paid_journal_id)
     */
    public function refreshProof(): void
    {
        $count = $this->attachments()->count();

        $this->forceFill([
            'proof_count' => $count,
            'has_proof' => $count > 0,
        ])->save();
    }
}
