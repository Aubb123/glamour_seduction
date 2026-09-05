<?php

namespace App\Services\V1\Controllers;

use App\Models\Audit;

class AuditService
{
    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function destroy(Audit $audit): void
    {
        $audit->delete();
    }

    // ─────────────────────────────────────────────
    // CLEAR OLD AUDITS (plus de X jours)
    // ─────────────────────────────────────────────
    public function clearOldAudits(int $days = 3): int
    {
        return Audit::where('created_at', '<', getDateTime()->subDays($days))->delete();
    }
}
