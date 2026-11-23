<?php

return [
    'warehouses' => [
        'default' => [
            'storage_required' => 'Un entrepôt par défaut doit obligatoirement être de type « stockage ».',
            'active_required' => 'Un entrepôt par défaut doit obligatoirement être actif.',
        ],
        'update' => [
            'default_release_forbidden' => 'Impossible de retirer le rôle d\'entrepôt par défaut tant qu\'un autre entrepôt n\'a pas été défini par défaut.',
            'default_deactivate_forbidden' => 'Impossible de désactiver l\'entrepôt par défaut tant qu\'un autre entrepôt n\'a pas pris sa place.',
            'default_type_forbidden' => 'Impossible de changer le type de l\'entrepôt par défaut tant qu\'un autre entrepôt n\'a pas pris sa place.',
        ],
        'change_status' => [
            'default_deactivate_forbidden' => 'Impossible de désactiver l\'entrepôt par défaut. Définissez d\'abord un autre entrepôt par défaut.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer un entrepôt actif. Veuillez le désactiver d\'abord.',
            'already_deleted' => 'Cet entrepôt est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les entrepôts déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'active_forbidden' => 'Impossible de supprimer définitivement un entrepôt actif.',
            'not_trashed_forbidden' => 'Seuls les entrepôts déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de l\'entrepôt (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        // ─────────────────────────────────────────────
        // Translations
        // ─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'warehouse_not_found' => 'Impossible de supprimer cette traduction : l\'entrepôt auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de l\'entrepôt.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
            ],
            'force_destroy' => [
                'warehouse_not_found' => 'Impossible de supprimer définitivement cette traduction : l\'entrepôt auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de l\'entrepôt.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],
    ],
];
