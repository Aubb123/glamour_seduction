<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait HasActivityLogs
{
    // Relation polymorphique vers la table activity_logs
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}
