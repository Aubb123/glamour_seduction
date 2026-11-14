<?php

return [
    'productWarehouses' => [
        'code' => [
            'required' => 'Le code est obligatoire.',
            'string' => 'Le code doit être une chaîne de caractères.',
            'max' => 'Le code ne peut pas dépasser 20 caractères.',
            'unique' => 'Le code doit être unique.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'translations' => [
            'required' => 'Les traductions sont obligatoires.',
            'array' => 'Les traductions doivent être un tableau.',
            'lang_id' => [
                'required' => 'La langue est obligatoire pour chaque traduction.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],
            'name' => [
                'required' => 'Le nom est obligatoire pour chaque traduction.',
                'string' => 'Le nom doit être une chaîne de caractères.',
                'max' => 'Le nom ne peut pas dépasser 255 caractères.',
                'unique' => 'Le nom doit être unique pour chaque langue.',
            ],
            'address' => [
                'string' => 'L\'adresse doit être une chaîne de caractères.',
                'max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            ],
            'city' => [
                'string' => 'La ville doit être une chaîne de caractères.',
                'max' => 'La ville ne peut pas dépasser 100 caractères.',
            ],
            'phone' => [
                'string' => 'Le téléphone doit être une chaîne de caractères.',
                'max' => 'Le téléphone ne peut pas dépasser 30 caractères.',
            ],
        ],
        'not_found' => 'L\'entrepôt n\'a pas été trouvé.',
    ],
    'warehouseUsers' => [
        'user_uuid' => [
            'required' => 'L\'utilisateur est obligatoire.',
            'exists' => 'L\'utilisateur sélectionné est introuvable.',
        ],
        'is_manager' => [
            'boolean' => 'Le champ "responsable" doit être un booléen.',
        ],
    ],
];
