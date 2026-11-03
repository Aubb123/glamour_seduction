<?php

return [

    'countries' => [

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'already_active' => 'Ce pays est déjà actif.',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'already_inactive' => 'Ce pays est déjà inactif.',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        // ─────────────────────────────────────────────
        // TRANSLATIONS DESTROY
        // ─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'country_not_found' => 'Impossible de supprimer cette traduction : le pays auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer cette traduction car c\'est la dernière traduction restante pour ce pays.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut pas être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être restaurées.',
            ],
            'force_destroy' => [
                'country_not_found' => 'Impossible de supprimer définitivement cette traduction : le pays auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement cette traduction car c\'est la dernière traduction restante pour ce pays.',
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être supprimées définitivement.',
            ],
        ],

    ],

];
