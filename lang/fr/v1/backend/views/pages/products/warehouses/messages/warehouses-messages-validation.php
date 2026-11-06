<?php

return [
    'productWarehouses' => [
        'code' => [
            'required' => 'Le code est obligatoire.',
            'string' => 'Le code doit être une chaîne de caractères.',
            'max' => 'Le code ne peut pas dépasser 20 caractères.',
            'unique' => 'Le code doit être unique.',
        ],
        'name' => [
            'required' => 'Le nom est obligatoire.',
            'string' => 'Le nom doit être une chaîne de caractères.',
            'max' => 'Le nom ne peut pas dépasser 255 caractères.',
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
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'is_default' => [
            'boolean' => 'Le champ "entrepôt par défaut" doit être un booléen.',
        ],
        'not_found' => 'L\'entrepôt n\'a pas été trouvé.',
    ],
    'warehouseUsers' => [
        'user_uuid' => [
            'required' => 'L\'utilisateur est obligatoire.',
            'exists' => 'L\'utilisateur sélectionné est introuvable.',
        ],
        'position_uuid' => [
            'exists' => 'Le poste sélectionné est introuvable.',
        ],
        'is_manager' => [
            'boolean' => 'Le champ "responsable" doit être un booléen.',
        ],
    ],
];
