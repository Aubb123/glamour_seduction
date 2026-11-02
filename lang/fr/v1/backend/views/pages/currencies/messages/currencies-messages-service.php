<?php

return [

    'currencies' => [

        // ─────────────────────────────────────────────
        // UPDATE (Module 10 : taux de change)
        // ─────────────────────────────────────────────
        'update' => [
            'exchange_rate_default_forbidden' => 'La devise par défaut est la base de saisie des prix : son taux de change doit rester fixé à 1.000000.',
        ],

        // ─────────────────────────────────────────────
        // PRICING (Module 10 : prix multi-devise)
        // ─────────────────────────────────────────────
        'pricing' => [
            'invalid_exchange_rate' => 'Conversion impossible : le taux de change de la devise « :code » est invalide ou non renseigné.',
        ],
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

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de la devise (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

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
