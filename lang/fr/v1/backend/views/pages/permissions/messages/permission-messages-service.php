<?php

return [
    'permissions' => [
        'activate' => [
            'already_active' => 'Cette permission est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette permission est déjà inactive.',
        ],
        'update' => [
            'cannot_rename_used' => 'Impossible de renommer cette permission car elle est utilisée par des rôles ou des utilisateurs.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une permission active. Veuillez la désactiver d\'abord.',
            'has_roles' => 'Impossible de supprimer cette permission car elle est utilisée par des rôles.',
            'has_users' => 'Impossible de supprimer cette permission car elle est utilisée par des utilisateurs.',
            'already_deleted' => 'Cette entité est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_deleted' => 'Cette permission n\'est pas supprimée.',
            'not_trashed_forbidden' => 'Seules les permissions déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les permissions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
