<?php

namespace App\Services\V1\Controllers;

use App\Enums\Subscribes\SubscribeStatus;
use App\Models\Subscribe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscribeService
{
    protected string $langPath = 'v1/backend/views/pages/subscribes/messages/subscribes-messages-service.subscribes';

    // ─────────────────────────────────────────────
    // SUBSCRIBE (création / réabonnement)
    // ─────────────────────────────────────────────
    public function subscribe(string $email, ?int $userId = null, bool $verified = false): Subscribe
    {
        return DB::transaction(function () use ($email, $userId, $verified) {

            $email = Str::lower(trim($email));

            // Recherche un abonné existant, y compris ceux placés en corbeille (soft delete)
            $existing = Subscribe::withTrashed()->where('email', $email)->first();

            // Cas 1 : l'abonné a été supprimé (soft) → on restaure la ligne existante au lieu d'en créer
            // une nouvelle, ce qui évite un conflit de contrainte unique (unique(email) + softDeletes)
            if ($existing?->trashed()) {
                $existing->restore();
                $existing->update([
                    'user_id' => $userId,
                    'status' => SubscribeStatus::Active,
                    'verified_at' => $verified ? getDateTime() : null,
                ]);
                $this->appendHistory($existing, 'resubscribed');

                return $existing;
            }

            // Cas 2 : l'abonnement existe toujours mais est inactif → simple réactivation
            if ($existing) {
                $existing->update([
                    'user_id' => $userId ?? $existing->user_id,
                    'status' => SubscribeStatus::Active,
                    'verified_at' => $verified ? getDateTime() : $existing->verified_at,
                ]);
                $this->appendHistory($existing, 'resubscribed');

                return $existing;
            }

            // Cas 3 : nouvel abonnement
            $subscribe = Subscribe::create([
                'user_id' => $userId,
                'email' => $email,
                'status' => SubscribeStatus::Active,
                'verified_at' => $verified ? getDateTime() : null,
            ]);
            $this->appendHistory($subscribe, 'subscribed');

            return $subscribe;
        });
    }

    /**
     * Enregistre une entrée dans l'historique des abonnements (traçabilité/audit).
     */
    protected function appendHistory(Subscribe $subscribe, string $action): void
    {
        $subscribe->histories()->create([
            'action' => $action,
            'acted_at' => getDateTime(),
            'ip_address' => app()->runningInConsole() ? '127.0.0.1' : request()->ip(),
        ]);
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(Subscribe $subscribe): void
    {
        DB::transaction(function () use ($subscribe) {

            $subscribe->update([
                'status' => SubscribeStatus::Active,
            ]);

        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(Subscribe $subscribe): void
    {
        DB::transaction(function () use ($subscribe) {

            $subscribe->update([
                'status' => SubscribeStatus::Inactive,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function destroy(Subscribe $subscribe): void
    {
        DB::transaction(function () use ($subscribe) {

            // Seul les abonnements inactifs peuvent être supprimés
            if ($subscribe->status === SubscribeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.flash_warning"));
            }

            if ($subscribe->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($subscribe);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function restore(Subscribe $subscribe): void
    {
        DB::transaction(function () use ($subscribe) {
            if ($subscribe->trashed()) {
                restore_function($subscribe);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function forceDestroy(Subscribe $subscribe): void
    {
        DB::transaction(function () use ($subscribe) {

            // Seul les abonnements inactifs peuvent être supprimés définitivement
            if ($subscribe->status === SubscribeStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.flash_warning"));
            }

            if ($subscribe->trashed()) {
                force_destroy_function($subscribe);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
