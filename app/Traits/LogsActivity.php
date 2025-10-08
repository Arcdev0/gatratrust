<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Simpan log aktivitas user.
     *
     * @param string $description
     * @param string|null $reference
     * @return void
     */
    public function logActivity(string $description, ?string $reference = null): void
    {
        ActivityLog::create([
            'user_id'     => Auth::id() ?? 0,
            'reference'   => $reference,
            'description' => $description,
            'created_at'  => now(),
        ]);
    }
}
