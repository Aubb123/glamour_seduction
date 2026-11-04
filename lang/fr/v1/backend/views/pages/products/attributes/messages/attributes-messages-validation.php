<?php

return [
    'productAttributes' => [
        'sort_order' => [
            'required' => 'L\'ordre de tri est obligatoire.',
            'integer' => 'L\'ordre de tri doit être un nombre entier.',
            'min' => 'L\'ordre de tri doit être au minimum 0.',
            'unique' => 'L\'ordre de tri doit être unique.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
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
            'description' => [
                'required' => 'La description est requise pour chaque traduction.',
                'string' => 'La description doit être une chaîne de caractères.',
                'max' => 'La description ne peut pas dépasser 1000 caractères.',
            ],
        ],
        'not_found' => 'L\'attribut de produits n\'a pas été trouvé.',
    ],
];
