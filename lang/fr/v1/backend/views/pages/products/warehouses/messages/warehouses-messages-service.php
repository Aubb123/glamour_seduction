<?php

return [
    'productWarehouses' => [
        'activate' => [
            'already_active' => 'Cet entrepôt est déjà actif.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cet entrepôt est déjà inactif.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer un entrepôt actif. Veuillez le désactiver d\'abord.',
            'already_deleted' => 'Cet entrepôt est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les entrepôts déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les entrepôts déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],
    ],
];
