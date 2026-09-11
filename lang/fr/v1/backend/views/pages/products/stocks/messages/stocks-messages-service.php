<?php

return [
    'productWarehouseStocks' => [
        'store' => [
            'pair_already_exists' => 'Un stock existe déjà pour ce couple (entrepôt, variante).',
        ],
        'update' => [
            'pair_already_exists' => 'Un stock existe déjà pour ce couple (entrepôt, variante).',
            'reserved_exceeds_quantity' => 'La quantité réservée ne peut pas dépasser la quantité en stock.',
        ],
        'destroy' => [
            'already_deleted' => 'Ce stock est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les stocks déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les stocks déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],
    ],
];
