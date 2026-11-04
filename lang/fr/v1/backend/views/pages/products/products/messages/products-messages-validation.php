<?php

return [
    'products' => [
        'category_id' => [
            'required' => 'La catégorie est obligatoire.',
            'integer' => 'La catégorie doit être un identifiant valide.',
            'exists' => 'La catégorie sélectionnée n\'existe pas.',
        ],
        'brand_id' => [
            'integer' => 'La marque doit être un identifiant valide.',
            'exists' => 'La marque sélectionnée n\'existe pas.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'is_featured' => [
            'boolean' => 'Le champ "mis en avant" doit être un booléen.',
        ],
        'requires_discreet_packaging' => [
            'boolean' => 'Le champ "emballage discret" doit être un booléen.',
        ],
        'weight' => [
            'numeric' => 'Le poids doit être un nombre.',
            'min' => 'Le poids doit être au minimum 0.',
            'max' => 'Le poids ne peut pas dépasser 99999.99 kg.',
        ],
        'base_price' => [
            'required' => 'Le prix de base est obligatoire.',
            'numeric' => 'Le prix de base doit être un nombre.',
            'min' => 'Le prix de base doit être supérieur à 0.',
            'max' => 'Le prix de base ne peut pas dépasser la valeur maximale autorisée.',
        ],
        'compare_at_price' => [
            'numeric' => 'Le prix barré doit être un nombre.',
            'min' => 'Le prix barré doit être supérieur à 0.',
            'max' => 'Le prix barré ne peut pas dépasser la valeur maximale autorisée.',
        ],
        'tags' => [
            'array' => 'Les tags doivent être un tableau.',
            'exists' => 'Un des tags sélectionnés n\'existe pas.',
        ],
        'translations' => [
            'required' => 'Les traductions sont obligatoires.',
            'array' => 'Les traductions doivent être un tableau.',
            'lang_id' => [
                'required' => 'La langue est requise pour chaque traduction.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],
            'name' => [
                'required' => 'Le nom est requis pour chaque traduction.',
                'string' => 'Le nom doit être une chaîne de caractères.',
                'max' => 'Le nom ne peut pas dépasser 255 caractères.',
                'unique' => 'Le nom doit être unique pour chaque langue.',
            ],
            'slug' => [
                'required' => 'Le slug est requis pour chaque traduction.',
                'string' => 'Le slug doit être une chaîne de caractères.',
                'max' => 'Le slug ne peut pas dépasser 255 caractères.',
                'unique' => 'Le slug doit être unique pour chaque langue.',
            ],
            'short_description' => [
                'required' => 'La description courte est requise pour chaque traduction.',
                'string' => 'La description courte doit être une chaîne de caractères.',
                'max' => 'La description courte ne peut pas dépasser 500 caractères.',
            ],
            'description' => [
                'required' => 'La description est requise pour chaque traduction.',
                'string' => 'La description doit être une chaîne de caractères.',
            ],
        ],
        'not_found' => 'Le produit n\'a pas été trouvé.',
    ],
];
