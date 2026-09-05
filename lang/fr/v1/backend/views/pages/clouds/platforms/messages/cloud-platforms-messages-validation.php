<?php

return [
    'cloud-platforms' => [
        'name' => [
            'required' => 'Le nom technique est obligatoire.',
            'string' => 'Le nom technique doit être une chaîne de caractères.',
            'max' => 'Le nom technique ne peut pas dépasser 255 caractères.',
        ],
        'label' => [
            'required' => 'Le label est obligatoire.',
            'string' => 'Le label doit être une chaîne de caractères.',
            'max' => 'Le label ne peut pas dépasser 255 caractères.',
        ],
        'api_endpoint' => [
            'required' => 'L\'endpoint API est obligatoire.',
            'string' => 'L\'endpoint API doit être une chaîne de caractères.',
            'max' => 'L\'endpoint API ne peut pas dépasser 255 caractères.',
            'url' => 'L\'endpoint API doit être une URL valide.',
        ],
        'api_key' => [
            'string' => 'La clé API doit être une chaîne de caractères.',
            'max' => 'La clé API ne peut pas dépasser 255 caractères.',
            'unique' => 'Cette clé API est déjà utilisée.',
        ],
        'api_secret' => [
            'string' => 'Le secret API doit être une chaîne de caractères.',
            'max' => 'Le secret API ne peut pas dépasser 255 caractères.',
        ],
        'disk_name' => [
            'required' => 'Le nom du disque est obligatoire.',
            'string' => 'Le nom du disque doit être une chaîne de caractères.',
            'max' => 'Le nom du disque ne peut pas dépasser :max caractères.',
            'in' => 'Le nom du disque sélectionné est invalide.',
        ],
        'description' => [
            'string' => 'La description doit être une chaîne de caractères.',
        ],
        'metadata' => [
            'json' => 'Les métadonnées doivent être un JSON valide.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'not_found' => 'La plateforme cloud n\'a pas été trouvée.',
    ],
];
