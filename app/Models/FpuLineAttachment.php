<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FpuLineAttachment extends Model
{
    protected $table = 'fpu_line_attachments';

    protected $fillable = [
        'fpu_line_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    protected $appends = ['file_url'];

    public function line(): BelongsTo
    {
        return $this->belongsTo(FpuLine::class, 'fpu_line_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }


    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
