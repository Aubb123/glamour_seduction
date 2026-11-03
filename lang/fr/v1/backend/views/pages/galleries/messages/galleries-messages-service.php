<?php

return [
    'galleries' => [
        'destroy' => [
            'active_gallery_forbidden' => 'La galerie (:name) est active et ne peut pas être supprimée.',
            'already_deleted' => 'Cette galerie est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les galeries déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_gallery_forbidden' => 'La galerie (:name) n\'est pas supprimée et ne peut pas être détruite définitivement.',
            'active_gallery_forbidden' => 'La galerie (:name) est active et ne peut pas être détruite définitivement.',
            'not_trashed_forbidden' => 'Seules les galeries déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
        'store' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour téléverser l\'image de la galerie.',
            'cloud_platform_inactive' => 'La plateforme cloud sélectionnée est inactive.',
            'cloud_platform_not_supported' => 'La plateforme cloud sélectionnée n\'est pas prise en charge pour cet upload.',
            'cloud_platform_folder_not_found' => 'Aucun dossier cloud n\'a été trouvé pour cette entité et ce type de fichier.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour téléverser l\'image de la galerie.',
        ],

        // ────────────────────────────────────────────
        // TRANSLATIONS
        // ────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'gallery_not_found' => 'Impossible de supprimer cette traduction : la galerie à laquelle elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'La traduction (:name) est la dernière traduction restante pour cette galerie et ne peut pas être supprimée.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
            ],
            'force_destroy' => [
                'gallery_not_found' => 'Impossible de supprimer définitivement cette traduction : la galerie à laquelle elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'La traduction (:name) est la dernière traduction restante pour cette galerie et ne peut pas être détruite définitivement.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],
    ],
];
