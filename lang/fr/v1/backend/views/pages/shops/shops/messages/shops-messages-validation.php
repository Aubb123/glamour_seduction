<?php

return [
    'shops' => [
        'code' => [
            'required' => 'Le code est obligatoire.',
            'string' => 'Le code doit être une chaîne de caractères.',
            'max' => 'Le code ne peut pas dépasser 20 caractères.',
            'unique' => 'Le code doit être unique.',
        ],
        'email' => [
            'email' => 'L\'email doit être une adresse email valide.',
            'max' => 'L\'email ne peut pas dépasser 255 caractères.',
        ],
        'phone' => [
            'string' => 'Le téléphone doit être une chaîne de caractères.',
            'max' => 'Le téléphone ne peut pas dépasser 30 caractères.',
        ],
        'address' => [
            'string' => 'L\'adresse doit être une chaîne de caractères.',
            'max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
        ],
        'city' => [
            'string' => 'La ville doit être une chaîne de caractères.',
            'max' => 'La ville ne peut pas dépasser 100 caractères.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'is_default' => [
            'boolean' => 'Le champ "boutique par défaut" doit être un booléen.',
        ],
        'sort_order' => [
            'required' => 'L\'ordre d\'affichage est obligatoire.',
            'integer' => 'L\'ordre d\'affichage doit être un entier.',
            'min' => 'L\'ordre d\'affichage ne peut pas être négatif.',
        ],
        'warehouses' => [
            'required' => 'Au moins un entrepôt est obligatoire.',
            'array' => 'Les entrepôts doivent être une liste.',
            'min' => 'Une boutique doit être rattachée à au moins un entrepôt.',
            'exists' => 'Un entrepôt sélectionné est introuvable.',
        ],
        'primary_warehouse_uuid' => [
            'exists' => 'L\'entrepôt principal sélectionné est introuvable.',
        ],
        'translations' => [
            'required' => 'Au moins une traduction est obligatoire.',
            'array' => 'Les traductions doivent être une liste.',
            'lang_id' => [
                'required' => 'La langue est obligatoire.',
                'exists' => 'La langue sélectionnée est introuvable.',
            ],
            'name' => [
                'required' => 'Le nom est obligatoire.',
                'string' => 'Le nom doit être une chaîne de caractères.',
                'max' => 'Le nom ne peut pas dépasser 255 caractères.',
                'unique' => 'Ce nom est déjà utilisé pour une autre boutique dans cette langue.',
            ],
            'description' => [
                'string' => 'La description doit être une chaîne de caractères.',
                'max' => 'La description ne peut pas dépasser 1000 caractères.',
            ],
        ],
    ],
];
