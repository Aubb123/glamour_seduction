<?php

namespace App\Services\V1\Controllers;

use App\Models\Lang;
use Illuminate\Support\Facades\DB;

use function __;

class LangService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/langs/messages/lang-messages-service.langs';

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_langs_activate(Lang $lang): void
    {
        DB::transaction(function () use ($lang) {

            // Activation de la langue
            $lang->update([
                'is_active' => true,
            ]);

        });
    }

    // ─────────────────────────────────────────────
    // DEACTIVATE
    // ─────────────────────────────────────────────
    public function backend_langs_deactivate(Lang $lang): void
    {
        DB::transaction(function () use ($lang) {

            // Vérifie que ce n'est pas la langue par défaut
            if ($lang->is_default) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.deactivate.default_forbidden"));
            }

            // Désactivation de la langue
            $lang->update([
                'is_active' => false,
            ]);

        });
    }
}
