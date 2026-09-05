<?php

return [
    'categories' => [
        'store' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger l\'image de la catégorie.',
            'cloud_platform_inactive' => 'La plateforme cloud sélectionnée est inactive.',
            'cloud_platform_not_supported' => 'La plateforme cloud sélectionnée n\'est pas prise en charge pour cet upload.',
            'cloud_platform_folder_not_found' => 'Aucun dossier cloud n\'a été trouvé pour cette entité et ce type de fichier.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger l\'image de la catégorie.',
        ],
        'activate' => [
            'already_active' => 'Cette catégorie est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette catégorie est déjà inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une catégorie active. Veuillez la désactiver d\'abord.',
            'has_posts_forbidden' => 'Impossible de supprimer une catégorie associée à des articles. Veuillez d\'abord supprimer ou réaffecter ses articles.',
            'already_deleted' => 'Cette catégorie est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les catégories déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les catégories déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],

    'translations' => [
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
