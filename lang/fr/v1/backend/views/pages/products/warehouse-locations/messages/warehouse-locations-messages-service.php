<?php

return [
    'warehouseLocations' => [
        'activate' => [
            'already_active' => 'Cet emplacement est déjà actif.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cet emplacement est déjà inactif.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer un emplacement actif. Veuillez le désactiver d\'abord.',
            'already_deleted' => 'Cet emplacement est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les emplacements déjà supprimés ou en corbeille peuvent être restaurés.',
            'parent_trashed' => 'Impossible de restaurer cet emplacement : son emplacement parent est en corbeille.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les emplacements déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
            'active_forbidden' => 'Impossible de supprimer définitivement un emplacement actif.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de l\'emplacement (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        // ─────────────────────────────────────────────
        // Translations
        // ─────────────────────────────────────────────
        'translations' => [
            'name' => [
                'unique_in_warehouse' => 'Le nom « :name » est déjà utilisé par un autre emplacement de cet entrepôt pour cette langue (corbeille incluse).',
            ],
            'destroy' => [
                'location_not_found' => 'Impossible de supprimer cette traduction : l\'emplacement auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
                'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de l\'emplacement.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
                'location_not_found' => 'Impossible de restaurer cette traduction : l\'emplacement auquel elle est liée est introuvable.',
                'name_conflict' => 'Impossible de restaurer cette traduction : son nom est déjà utilisé par un autre emplacement de cet entrepôt pour cette langue.',
            ],
            'force_destroy' => [
                'location_not_found' => 'Impossible de supprimer définitivement cette traduction : l\'emplacement auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de l\'emplacement.',
            ],
        ],
    ],
];
