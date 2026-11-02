<?php

return [
    'productCategories' => [
        'store' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger l\'image de la catégorie.',
            'cloud_platform_inactive' => 'La plateforme cloud sélectionnée est inactive.',
            'cloud_platform_not_supported' => 'La plateforme cloud sélectionnée n\'est pas prise en charge pour cet upload.',
            'cloud_platform_folder_not_found' => 'Aucun dossier cloud n\'a été trouvé pour cette entité et ce type de fichier.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger l\'image de la catégorie.',
            'parent_self_forbidden' => 'Une catégorie ne peut pas être sa propre catégorie parente.',
            'parent_descendant_forbidden' => 'Impossible de rattacher cette catégorie à l\'une de ses propres sous-catégories : cela créerait un cycle dans l\'arborescence.',
        ],
        'activate' => [
            'already_active' => 'Cette catégorie est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette catégorie est déjà inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une catégorie active. Veuillez la désactiver d\'abord.',
            'already_deleted' => 'Cette catégorie est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les catégories déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les catégories déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],

    'productCategoriesTranslations' => [
        'destroy' => [
            'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de la catégorie.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de la catégorie.',
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
