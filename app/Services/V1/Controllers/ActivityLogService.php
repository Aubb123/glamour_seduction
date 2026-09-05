<?php

namespace App\Services\V1\Controllers;

use App\Models\ActivityLog;

class ActivityLogService
{
    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(ActivityLog $log): void
    {
        $log->delete();
    }

    // ─────────────────────────────────────────────
    // CLEAR OLD LOGS (plus de X jours)
    // ─────────────────────────────────────────────
    public function clearOldLogs(int $days = 3): int
    {
        return ActivityLog::where('created_at', '<', getDateTime()->subDays($days))->delete();
    }
}
