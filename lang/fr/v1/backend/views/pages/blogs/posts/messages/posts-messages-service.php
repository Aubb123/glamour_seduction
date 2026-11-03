<?php

return [
    'posts' => [
        'scheduled_date_required' => 'Une date de publication est requise pour programmer un article.',
        'scheduled_date_past' => 'La date de publication programmée doit être dans le futur.',

        'store' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger l\'image de l\'article.',
            'cloud_platform_inactive' => 'La plateforme cloud sélectionnée est inactive.',
            'cloud_platform_not_supported' => 'La plateforme cloud sélectionnée n\'est pas prise en charge pour cet upload.',
            'cloud_platform_folder_not_found' => 'Aucun dossier cloud n\'a été trouvé pour cette entité et ce type de fichier.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour télécharger l\'image de l\'article.',
        ],
        'destroy' => [
            'published_forbidden' => 'Impossible de supprimer un article publié. Veuillez le passer en brouillon d\'abord.',
            'already_deleted' => 'Cet article est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les articles déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les articles déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],

        // ─────────────────────────────────────────────
        // Translations
        // ─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'post_not_found' => 'Impossible de supprimer cette traduction : l\'article auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de l\'article.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
            ],
            'force_destroy' => [
                'post_not_found' => 'Impossible de supprimer définitivement cette traduction : l\'article auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de l\'article.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],
    ],
];
