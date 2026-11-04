<?php

return [
    'productVariants' => [
        'product_id' => [
            'required' => 'Le produit parent est obligatoire.',
            'integer' => 'Le produit parent doit être un identifiant valide.',
            'exists' => 'Le produit parent sélectionné n\'existe pas.',
        ],
        'sku' => [
            'required' => 'La référence SKU est obligatoire.',
            'string' => 'La référence SKU doit être une chaîne de caractères.',
            'max' => 'La référence SKU ne peut pas dépasser 100 caractères.',
            'unique' => 'La référence SKU doit être unique.',
        ],
        'barcode' => [
            'string' => 'Le code-barres doit être une chaîne de caractères.',
            'max' => 'Le code-barres ne peut pas dépasser 100 caractères.',
        ],
        'price' => [
            'numeric' => 'Le prix doit être un nombre.',
            'min' => 'Le prix doit être supérieur à 0 (laissez vide pour hériter du prix du produit).',
            'max' => 'Le prix ne peut pas dépasser la valeur maximale autorisée.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'is_default' => [
            'boolean' => 'Le champ "variante par défaut" doit être un booléen.',
        ],
        'attribute_value_ids' => [
            'array' => 'Les valeurs d\'attributs doivent être un tableau.',
            'exists' => 'Une des valeurs d\'attributs sélectionnées n\'existe pas.',
        ],
        'not_found' => 'La variante de produit n\'a pas été trouvée.',
    ],
];
