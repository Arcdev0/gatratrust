<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fpu extends Model
{
    protected $table = 'fpus';

    protected $fillable = [
        'fpu_no',
        'project_id',
        'request_date',
        'requester_id',
        'requester_name',
        'purpose',
        'notes',
        'wallet_coa_id',
        'total_amount',
        'status',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejected_reason',

        // ✅ NEW
        'approve_journal_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'total_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // ======================
    // ENUM VALUES
    // ======================
    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_PAID      = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public const PURPOSE_TAGIHAN   = 'tagihan_rutin';
    public const PURPOSE_MATERIAL  = 'pembelian_material';
    public const PURPOSE_AKOMODASI = 'akomodasi_operasional';
    public const PURPOSE_VENDOR    = 'bayar_vendor';
    public const PURPOSE_LAINNYA   = 'lainnya';

    // ======================
    // RELATIONS
    // ======================
    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectTbl::class, 'project_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function walletCoa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'wallet_coa_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(FpuLine::class, 'fpu_id')->orderBy('line_no');
    }

    // ✅ NEW: approve journal relation
    public function approveJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'approve_journal_id');
    }

    // ======================
    // HELPERS
    // ======================

    public function recalcTotal(): void
    {
        $total = $this->lines()->sum('amount');
        $this->forceFill(['total_amount' => $total])->save();
    }

    /**
     * Paid bila semua line sudah punya paid_journal_id (lebih akurat dari sekadar has_proof).
     */
    public function allLinesPaid(): bool
    {
        $total = $this->lines()->count();
        if ($total === 0) return false;

        $notPaid = $this->lines()->whereNull('paid_journal_id')->count();
        return $notPaid === 0;
    }

    /**
     * Update status to PAID when approved and all lines paid.
     */
    public function refreshPaidStatus(): void
    {
        if ($this->status !== self::STATUS_APPROVED) {
            return;
        }

        if ($this->allLinesPaid()) {
            $this->forceFill(['status' => self::STATUS_PAID])->save();
        }
    }
}
