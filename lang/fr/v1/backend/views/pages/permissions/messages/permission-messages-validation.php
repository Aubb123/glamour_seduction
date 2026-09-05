<?php

return [
    'permissions' => [
        'name' => [
            'required' => 'Le nom de la permission est requis.',
            'string' => 'Le nom de la permission doit être une chaîne de caractères.',
            'max' => 'Le nom de la permission ne peut pas dépasser 255 caractères.',
            'unique' => 'Ce nom de permission existe déjà.',
            'regex' => 'Le nom de la permission doit contenir uniquement des lettres minuscules, des chiffres, des tirets, des tirets bas et des points.',
        ],
        'guard_name' => [
            'required' => 'Le nom du guard est requis.',
            'string' => 'Le nom du guard doit être une chaîne de caractères.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'roles' => [
            'array' => 'Les rôles doivent être un tableau.',
            'exists' => 'L\'un des rôles sélectionnés n\'existe pas.',
        ],
    ],
];
