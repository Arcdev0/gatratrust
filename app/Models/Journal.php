<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $table = 'journals';
    public $timestamps = true;

    protected $fillable = [
        'journal_no',
        'journal_date',
        'type',
        'category',
        'reference_no',
        'source_module',
        'source_id',
        'memo',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'journal_date' => 'date',
        'posted_at' => 'datetime',
        'source_id' => 'integer',
        'created_by' => 'integer',
        'posted_by' => 'integer',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'journal_id')->orderBy('line_no');
    }

    /**
     * Helper hitung total debit & credit dari relasi lines
     * (pakai setelah load('lines'))
     */
    public function getTotalDebitAttribute(): float
    {
        return (float) ($this->lines->sum('debit') ?? 0);
    }

    public function getTotalCreditAttribute(): float
    {
        return (float) ($this->lines->sum('credit') ?? 0);
    }

    public function getIsBalancedAttribute(): bool
    {
        // biar aman floating: pakai 2 desimal
        return round($this->total_debit, 2) === round($this->total_credit, 2);
    }
}
