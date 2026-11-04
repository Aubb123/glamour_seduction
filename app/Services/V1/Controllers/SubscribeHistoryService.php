<?php

namespace App\Services\V1\Controllers;

use App\Models\SubscribeHistory;
use Illuminate\Support\Facades\DB;

class SubscribeHistoryService
{
    protected string $langPath = 'v1/backend/views/pages/subscribes/messages/subscribes-messages-service.subscribes.histories';

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_destroy(SubscribeHistory $subscribeHistory): void
    {
        // Retourne une exception à titre informatif, l'historique des abonnements ne peut pas être supprimé, il est conservé à des fins de traçabilité et d'audit.
        throw new \InvalidArgumentException(__("{$this->langPath}.destroy.flash_warning"));
        DB::transaction(function () use ($subscribeHistory) {
            if ($subscribeHistory->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($subscribeHistory);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_restore(SubscribeHistory $subscribeHistory): void
    {
        DB::transaction(function () use ($subscribeHistory) {
            if ($subscribeHistory->trashed()) {
                restore_function($subscribeHistory);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_subscribes_histories_force_destroy(SubscribeHistory $subscribeHistory): void
    {
        // Retourne une exception à titre informatif, l'historique des abonnements ne peut pas être supprimé, il est conservé à des fins de traçabilité et d'audit.
        throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.flash_warning"));
        DB::transaction(function () use ($subscribeHistory) {
            if ($subscribeHistory->trashed()) {
                force_destroy_function($subscribeHistory);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
