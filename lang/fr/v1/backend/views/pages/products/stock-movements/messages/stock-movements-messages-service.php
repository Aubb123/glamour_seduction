<?php

return [
    'productStockMovements' => [
        'record' => [
            'invalid_type' => 'Le type de mouvement sélectionné est invalide.',
            'quantity_must_be_positive' => 'La quantité du mouvement doit être strictement positive.',
            'negative_stock_forbidden' => 'Mouvement refusé : le stock résultant serait négatif (stock actuel : :stock).',
        ],
        'destroy' => [
            'already_deleted' => 'Ce mouvement est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les mouvements déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les mouvements déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],
    ],
];
