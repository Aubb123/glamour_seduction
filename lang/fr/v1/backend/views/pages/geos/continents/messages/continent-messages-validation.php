<?php

return [
    // Code
    'code' => [
        'required' => 'Le code continent est obligatoire.',
        'string' => 'Le code continent doit être une chaîne de caractères.',
        'max' => 'Le code continent ne doit pas dépasser :max caractères.',
        'unique' => 'Ce code continent est déjà utilisé.',
        'regex' => 'Le code continent doit être en lettres majuscules uniquement (ex: AF, EU, AS).',
    ],

    // Population
    'population' => [
        'required' => 'La population est obligatoire.',
        'integer' => 'La population doit être un nombre entier.',
        'min' => 'La population ne peut pas être négative.',
    ],

    // area_km2
    'area_km2' => [
        'required' => 'La superficie est obligatoire.',
        'numeric' => 'La superficie doit être un nombre.',
        'min' => 'La superficie ne peut pas être négative.',
    ],

    // Sort order
    'sort_order' => [
        'required' => 'L\'ordre d\'affichage est obligatoire.',
        'integer' => 'L\'ordre d\'affichage doit être un nombre entier.',
        'min' => 'L\'ordre d\'affichage ne peut pas être négatif.',
        'unique' => 'L\'ordre d\'affichage est déjà utilisé.',
    ],

    // Statut
    'status' => [
        'required' => 'Le statut est obligatoire.',
        'in' => 'Le statut sélectionné est invalide.',
    ],

    // Translations
    'translations' => [
        'required' => 'Au moins une traduction est requise.',
        'array' => 'Les traductions doivent être fournies sous forme de tableau.',
        'min' => 'Au moins une traduction est requise.',

        'lang_id' => [
            'required' => 'La langue est obligatoire pour chaque traduction.',
            'integer' => 'La langue doit être un identifiant valide.',
            'exists' => 'La langue sélectionnée n\'existe pas.',
        ],

        'name' => [
            'required' => 'Le nom est obligatoire pour chaque traduction.',
            'string' => 'Le nom doit être une chaîne de caractères.',
            'max' => 'Le nom ne doit pas dépasser :max caractères.',
        ],

        'description' => [
            'required' => 'La description est obligatoire pour chaque traduction.',
            'string' => 'La description doit être une chaîne de caractères.',
        ],
    ],
];
