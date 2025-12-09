<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'no_ref',
        'invoice_type',
        'date',
        'project_id',
        'customer_name',
        'customer_address',
        'description',
        'gross_total',
        'discount',
        'down_payment',
        'tax',
        'net_total',
        'status',
        'approval_status',
        'user_approve',
        'approved_at',
        'rejected_at',
        'reject_reason',
        'approved_qr',
        'signature_token',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relasi ke tabel pembayaran
    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount_paid');
    }

    public function getRemainingAttribute()
    {
        return $this->net_total - $this->total_paid;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'project_id');
    }

    public function getTotalInvoiceAttribute()
    {
        return $this->invoices()->sum('net_total');
    }

    public function getIsLunasAttribute()
    {
        return $this->total_invoice >= $this->total_biaya_project;
    }

    // ---------- APPROVAL RELATION & ACCESSOR ----------

    // user yang approve/reject
    public function approver()
    {
        return $this->belongsTo(User::class, 'user_approve');
    }

    // helper: apakah sudah approved?
    public function getIsApprovedAttribute()
    {
        return $this->approval_status === 'approved';
    }

    // helper: apakah ditolak?
    public function getIsRejectedAttribute()
    {
        return $this->approval_status === 'rejected';
    }

    // helper: pending
    public function getIsPendingAttribute()
    {
        return $this->approval_status === 'pending';
    }

    // optional: fungsi untuk approve lewat model
    public function approve($userId)
    {
        $this->update([
            'approval_status' => 'approved',
            'user_approve'    => $userId,
            'approved_at'     => now(),
            'rejected_at'     => null,
            'reject_reason'   => null,
        ]);
    }

    // optional: fungsi untuk reject lewat model
    public function reject($userId, $reason = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'user_approve'    => $userId,
            'rejected_at'     => now(),
            'reject_reason'   => $reason,
            'approved_at'     => null,
        ]);
    }
}
