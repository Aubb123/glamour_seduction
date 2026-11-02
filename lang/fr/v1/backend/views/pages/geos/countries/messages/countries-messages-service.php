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

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction du pays (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        // ─────────────────────────────────────────────
        // TRANSLATIONS DESTROY
        // ─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Impossible de supprimer cette traduction car c\'est la dernière traduction restante pour ce pays.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut pas être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être restaurées.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Impossible de supprimer définitivement cette traduction car c\'est la dernière traduction restante pour ce pays.',
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être supprimées définitivement.',
            ],
        ],

    ],

];
