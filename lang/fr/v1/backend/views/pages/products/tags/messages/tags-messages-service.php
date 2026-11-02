<?php

return [
    'productTags' => [
        'store' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger le logo de la tag.',
            'cloud_platform_inactive' => 'La plateforme cloud sélectionnée est inactive.',
            'cloud_platform_not_supported' => 'La plateforme cloud sélectionnée n\'est pas prise en charge pour cet upload.',
            'cloud_platform_folder_not_found' => 'Aucun dossier cloud n\'a été trouvé pour cette entité et ce type de fichier.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger le logo de la tag.',
        ],
        'activate' => [
            'already_active' => 'Cette tag est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette tag est déjà inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une tag active. Veuillez la désactiver d\'abord.',
            'already_deleted' => 'Cette tag est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les tags déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les tags déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction du tag (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',
    ],

    'productTagsTranslations' => [
        'destroy' => [
            'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de la tag.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de la tag.',
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
