<?php

return [

    'currencies' => [

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'already_active' => 'Cette devise est déjà active.',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'already_inactive' => 'Cette devise est déjà inactive.',
        ],

        // ─────────────────────────────────────────────
        // DESTROY
        // ─────────────────────────────────────────────
        'destroy' => [
            'default_currency_forbidden' => 'Impossible de supprimer cette devise car elle est définie comme devise par défaut de la plateforme. Désignez d\'abord une autre devise par défaut.',
            'already_deleted' => 'Cette devise est déjà supprimée et ne peut pas être supprimée à nouveau.',
        ],

        // ─────────────────────────────────────────────
        // RESTORE
        // ─────────────────────────────────────────────
        'restore' => [
            'not_trashed_forbidden' => 'Seules les devises supprimées peuvent être restaurées.',
        ],

        // ─────────────────────────────────────────────
        // FORCE DESTROY
        // ─────────────────────────────────────────────
        'force_destroy' => [
            'default_currency_forbidden' => 'Impossible de supprimer définitivement cette devise car elle est définie comme devise par défaut de la plateforme. Désignez d\'abord une autre devise par défaut.',
            'not_trashed_forbidden' => 'Seules les devises supprimées peuvent être supprimées définitivement.',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Impossible de supprimer cette traduction car c\'est la dernière traduction restante pour cette devise.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut pas être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être restaurées.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Impossible de supprimer définitivement cette traduction car c\'est la dernière traduction restante pour cette devise.',
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être supprimées définitivement.',
            ],
        ],

    ],

];
