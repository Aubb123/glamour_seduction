<?php

return [

    'divisions' => [

        'activate' => [
            'already_active' => 'Cette division est déjà active.',
        ],

        'deactivate' => [
            'already_inactive' => 'Cette division est déjà inactive.',
        ],

        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une division active. Désactivez-la d\'abord.',
            'already_deleted' => 'Cette division est déjà supprimée.',
            'has_children' => 'Impossible de supprimer cette division : elle possède des sous-divisions. Veuillez d\'abord supprimer ses sous-divisions.',
        ],

        'restore' => [
            'not_trashed_forbidden' => 'Seules les divisions supprimées peuvent être restaurées.',
            'parent_trashed' => 'Impossible de restaurer cette division : sa division parente se trouve dans la corbeille. Restaurez d\'abord la parente.',
        ],

        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les divisions supprimées peuvent être supprimées définitivement.',
            'active_forbidden' => 'Impossible de supprimer définitivement une division active.',
            'has_children' => 'Impossible de supprimer définitivement cette division : elle possède des sous-divisions (même en corbeille).',
        ],

        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de la division (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        'translations' => [

            'destroy' => [
                'division_not_found' => 'Impossible de supprimer cette traduction : la division à laquelle elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer cette traduction car c\'est la dernière traduction restante pour cette division.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut pas être supprimée à nouveau.',
            ],

            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être restaurées.',
                'slug_conflict' => 'Impossible de restaurer cette traduction : le slug est déjà utilisé par une autre traduction dans la même langue.',
            ],

            'force_destroy' => [
                'division_not_found' => 'Impossible de supprimer définitivement cette traduction : la division à laquelle elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement cette traduction car c\'est la dernière traduction restante pour cette division.',
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être supprimées définitivement.',
            ],
        ],

    ],

];