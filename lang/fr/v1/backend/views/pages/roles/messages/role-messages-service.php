<?php

return [
    'roles' => [
        'activate' => [
            'already_active' => 'Ce rôle est déjà actif.',
        ],
        'deactivate' => [
            'already_inactive' => 'Ce rôle est déjà inactif.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction du rôle (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        'translations' => [
            'destroy' => [
                'role_not_found' => 'Impossible de supprimer cette traduction : le rôle auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer cette traduction car c\'est la dernière traduction restante pour ce rôle.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
            ],
            'force_destroy' => [
                'role_not_found' => 'Impossible de supprimer définitivement cette traduction : le rôle auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement cette traduction car c\'est la dernière traduction restante pour ce rôle.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],
    ],
];
