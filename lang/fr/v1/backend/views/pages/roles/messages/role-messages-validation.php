<?php

return [
    // Nom interne
    'name' => [
        'required' => 'Le nom interne du rôle est obligatoire.',
        'string' => 'Le nom interne doit être une chaîne de caractères.',
        'max' => 'Le nom interne ne peut pas dépasser 255 caractères.',
    ],

    // Guard name
    'guard_name' => [
        'required' => 'Le guard est obligatoire.',
        'string' => 'Le guard doit être une chaîne de caractères.',
        'max' => 'Le guard ne peut pas dépasser 255 caractères.',
    ],

    // Statut
    'status' => [
        'required' => 'Le statut est obligatoire.',
        'in' => 'Le statut sélectionné n\'est pas valide.',
    ],

    // Traductions
    'translations' => [
        'required' => 'Au moins une traduction est obligatoire.',
        'array' => 'Les traductions doivent être un tableau.',
        'min' => 'Au moins une traduction est obligatoire.',

        // Langue
        'lang_id' => [
            'required' => 'La langue de la traduction est obligatoire.',
            'integer' => 'L\'identifiant de la langue doit être un entier.',
            'exists' => 'La langue sélectionnée n\'existe pas.',
        ],

        // Libellé
        'label' => [
            'required' => 'Le libellé du rôle est obligatoire.',
            'string' => 'Le libellé doit être une chaîne de caractères.',
            'max' => 'Le libellé ne peut pas dépasser 255 caractères.',
        ],

        // Description
        'description' => [
            'required' => 'La description est obligatoire.',
            'string' => 'La description doit être une chaîne de caractères.',
        ],
    ],
];
