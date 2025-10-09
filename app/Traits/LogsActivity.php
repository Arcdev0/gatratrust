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
     * @param array|null $oldData
     * @param array|null $newData
     * @return void
     */
    public function logActivity(string $description, ?string $reference = null, ?array $oldData = null, ?array $newData = null): void
    {
        ActivityLog::create([
            'user_id'     => Auth::id() ?? 0,
            'reference'   => $reference,
            'description' => $description,
            'old_data'    => $oldData ? json_encode($oldData) : null,
            'new_data'    => $newData ? json_encode($newData) : null,
            'created_at'  => now(),
        ]);
    }
}
