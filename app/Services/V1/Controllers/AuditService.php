<?php

namespace App\Services\V1\Controllers;

use App\Models\Audit;

class AuditService
{
    // ─────────────────────────────────────────────
    // CLEAR OLD AUDITS (plus de X jours)
    // ─────────────────────────────────────────────
    public function backend_audits_clear_old_audits(int $days = 3): int
    {
        return Audit::where('created_at', '<', getDateTime()->subDays($days))->delete();
    }
}
