<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalLine extends Model
{
    protected $table = 'journal_lines';
    public $timestamps = true;

    protected $fillable = [
        'journal_id',
        'coa_id',
        'description',
        'debit',
        'credit',
        'project_id',
        'customer_id',
        'vendor_id',
        'wallet_id',
        'line_no',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'line_no' => 'integer',
        'journal_id' => 'integer',
        'coa_id' => 'integer',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }
}
