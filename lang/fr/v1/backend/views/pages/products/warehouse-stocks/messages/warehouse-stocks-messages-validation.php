<?php

return [
    'warehouseStocks' => [
        'warehouse_id' => [
            'required' => 'L\'entrepôt est obligatoire.',
            'integer' => 'L\'entrepôt doit être un identifiant valide.',
            'exists' => 'L\'entrepôt sélectionné n\'existe pas.',
        ],
        'product_variant_id' => [
            'required' => 'La variante est obligatoire.',
            'integer' => 'La variante doit être un identifiant valide.',
            'exists' => 'La variante sélectionnée n\'existe pas.',
        ],
        'quantity' => [
            'required' => 'La quantité est obligatoire.',
            'integer' => 'La quantité doit être un nombre entier.',
            'min' => 'La quantité ne peut pas être négative.',
        ],
    ],
];
