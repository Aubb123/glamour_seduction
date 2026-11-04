<?php

namespace App\Services\V1\Controllers;

use App\Models\ActivityLog;

class ActivityLogService
{
    // ─────────────────────────────────────────────
    // CLEAR OLD LOGS (plus de X jours)
    // ─────────────────────────────────────────────
    public function backend_activity_logs_clear_old_logs(int $days = 3): int
    {
        return ActivityLog::where('created_at', '<', getDateTime()->subDays($days))->delete();
    }
}
