<?php

return [
    'warehouseLocationStocks' => [
        'warehouse_not_tracked' => 'Cet emplacement appartient à un entrepôt qui n\'est pas configuré pour un suivi par emplacement (mode « warehouse »). Le stock par emplacement n\'est pas autorisé ici.',
        'warehouse_mismatch' => 'L\'emplacement sélectionné n\'appartient pas à l\'entrepôt indiqué.',
        'store' => [
            'already_exists' => 'Un stock existe déjà pour cette variante dans cet emplacement.',
            'negative_quantity' => 'La quantité ne peut pas être négative.',
        ],
        'update' => [
            'negative_quantity' => 'La quantité ne peut pas être négative.',
        ],
    ],
];
