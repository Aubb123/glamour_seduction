<?php

return [
    'productStockMovements' => [
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
        'type' => [
            'required' => 'Le type de mouvement est obligatoire.',
            'string' => 'Le type de mouvement doit être une chaîne de caractères.',
            'in' => 'Le type de mouvement sélectionné n\'est pas valide.',
        ],
        'quantity' => [
            'required' => 'La quantité est obligatoire.',
            'integer' => 'La quantité doit être un nombre entier.',
            'min' => 'La quantité doit être strictement supérieure à zéro.',
        ],
        'reference' => [
            'incomplete' => 'La référence doit être complète : fournissez le type ET l\'identifiant ensemble.',
        ],
        'reference_type' => [
            'string' => 'Le type de référence doit être une chaîne de caractères.',
            'max' => 'Le type de référence ne peut pas dépasser 255 caractères.',
        ],
        'reference_id' => [
            'integer' => 'L\'identifiant de référence doit être un nombre entier.',
            'min' => 'L\'identifiant de référence doit être positif.',
        ],
        'note' => [
            'string' => 'La note doit être une chaîne de caractères.',
            'max' => 'La note ne peut pas dépasser 1000 caractères.',
        ],
        'not_found' => 'Le mouvement de stock n\'a pas été trouvé.',
    ],
];
