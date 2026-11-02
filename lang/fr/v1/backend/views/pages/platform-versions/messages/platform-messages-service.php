<?php

return [
    'versions' => [
        'update' => [
            'unpublish_forbidden' => 'Une version publiée ne peut pas être dépubliée. Veuillez contacter le développeur si vous souhaitez dépublier cette version.',
        ],
        'destroy' => [
            'published_forbidden' => 'Une version publiée ne peut pas être supprimée. Veuillez contacter le développeur si vous souhaitez supprimer cette version.',
            'active_forbidden' => 'Une version active ne peut pas être supprimée. Veuillez d\'abord désactiver cette version avant de la supprimer.',
            'already_deleted' => 'Cette version est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'publish' => [
            'already_published' => 'Cette version est déjà publiée. Veuillez sélectionner une autre version à publier.',
        ],
        'unpublish' => [
            'published_forbidden' => 'Une version publiée ne peut pas être dépubliée. Veuillez contacter le développeur si vous souhaitez dépublier cette version.',
            'already_unpublished' => 'Cette version est déjà dépubliée. Veuillez sélectionner une autre version à dépublier.',
        ],
        'activate' => [
            'already_active' => 'Cette version est déjà active. Veuillez sélectionner une autre version à activer.',
            'published_required' => 'Seules les versions publiées peuvent être activées. Veuillez publier cette version avant de l\'activer.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette version est déjà inactive. Veuillez sélectionner une autre version à désactiver.',
        ],

        'restore' => [
            'not_trashed_forbidden' => 'Seules les versions déjà supprimées ou en corbeille peuvent être restaurées.',
            'code_conflict' => 'Impossible de restaurer cette version : son code est déjà utilisé par une autre version active.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les versions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de la version (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Cette traduction ne peut pas être supprimée car il s\'agit de la dernière traduction associée à cette version.',
                'version_not_found' => 'La version associée à cette traduction n\'a pas été trouvée. Veuillez vérifier que la version existe et réessayer.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
                'lang_conflict' => 'Impossible de restaurer cette traduction : une autre traduction active existe déjà pour cette langue.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Cette traduction ne peut pas être supprimée définitivement car il s\'agit de la dernière traduction associée à cette version.',
                'version_not_found' => 'La version associée à cette traduction n\'a pas été trouvée. Veuillez vérifier que la version existe et réessayer.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],

    ],
];
