<?php

return [
    'productVariants' => [
        'activate' => [
            'already_active' => 'Cette variante est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette variante est déjà inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une variante active. Veuillez la désactiver d\'abord.',
            'already_deleted' => 'Cette variante est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les variantes déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les variantes déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];