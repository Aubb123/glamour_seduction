<?php

return [
    'tags' => [
        'activate' => [
            'already_active' => 'Ce tag est déjà actif.',
        ],
        'deactivate' => [
            'already_inactive' => 'Ce tag est déjà inactif.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer un tag actif. Veuillez le désactiver d\'abord.',
            'already_deleted' => 'Ce tag est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les tags déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les tags déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],
        // --─────────────────────────────────────────────
        // TRANSLATIONS
        // --─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction du tag.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction du tag.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],
    ],
];
