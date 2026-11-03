<?php

return [
    'tags' => [
        'activate' => [
            'already_active' => 'Ce tag est déjà actif.',
        ],
        'deactivate' => [
            'already_inactive' => 'Ce tag est déjà inactif.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer un tag actif. Veuillez le désactiver d\'abord.',
            'already_deleted' => 'Ce tag est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les tags déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les tags déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction du tag (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        // --─────────────────────────────────────────────
        // TRANSLATIONS
        // --─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'tag_not_found' => 'Impossible de supprimer cette traduction : le tag auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction du tag.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
            ],
            'force_destroy' => [
                'tag_not_found' => 'Impossible de supprimer définitivement cette traduction : le tag auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction du tag.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],
    ],
];
