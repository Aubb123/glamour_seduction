<?php

return [
    'productWarehouseStocks' => [
        'warehouse_id' => [
            'required' => 'L\'entrepôt est obligatoire.',
            'integer' => 'L\'entrepôt sélectionné n\'est pas valide.',
            'exists' => 'L\'entrepôt sélectionné n\'existe pas.',
        ],
        'product_variant_id' => [
            'required' => 'La variante de produit est obligatoire.',
            'integer' => 'La variante sélectionnée n\'est pas valide.',
            'exists' => 'La variante sélectionnée n\'existe pas.',
        ],
        'quantity' => [
            'required' => 'La quantité est obligatoire.',
            'integer' => 'La quantité doit être un nombre entier.',
            'min' => 'La quantité ne peut pas être négative.',
        ],
        'reserved_quantity' => [
            'integer' => 'La quantité réservée doit être un nombre entier.',
            'min' => 'La quantité réservée ne peut pas être négative.',
            'exceeds_quantity' => 'La quantité réservée ne peut pas dépasser la quantité en stock (max :quantity).',
        ],
        'low_stock_threshold' => [
            'required' => 'Le seuil d\'alerte de stock bas est obligatoire.',
            'integer' => 'Le seuil d\'alerte doit être un nombre entier.',
            'min' => 'Le seuil d\'alerte ne peut pas être négatif.',
        ],
        'not_found' => 'Le stock n\'a pas été trouvé.',
    ],
];
