<?php

return [
    'subscribes' => [
        'destroy' => [
            'flash_warning' => 'Vous ne pouvez pas supprimer un abonnement actif.',
            'already_deleted' => 'Cette entité est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'force_destroy' => [
            'flash_warning' => 'Vous ne pouvez pas supprimer définitivement un abonnement actif.',
            'not_trashed_forbidden' => 'Seuls les abonnements déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],

        // ────────────────────────────────────────────
        // HISTORIQUE DES ABONNEMENTS
        // ────────────────────────────────────────────
        'histories' => [
            'destroy' => [
                'flash_warning' => 'L\'historique des abonnements ne peut pas être supprimé, il est conservé à des fins de traçabilité et d\'audit.',
                'already_deleted' => 'Cette entité est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'force_destroy' => [
                'flash_warning' => 'L\'historique des abonnements ne peut pas être supprimé définitivement, il est conservé à des fins de traçabilité et d\'audit.',
                'not_trashed_forbidden' => 'Seules les historiques d\'abonnement déjà supprimés ou en corbeille peuvent être supprimées définitivement.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les historiques d\'abonnement déjà supprimés ou en corbeille peuvent être restaurées.',
            ],
        ],

    ],
];
