<?php

return [
    // Code
    'code' => [
        'required' => 'Le code devise est obligatoire.',
        'string' => 'Le code devise doit être une chaîne de caractères.',
        'size' => 'Le code devise doit contenir exactement 3 caractères.',
        'regex' => 'Le code devise doit contenir uniquement des lettres majuscules (ex: EUR, USD, XOF).',
        'unique' => 'Ce code devise est déjà utilisé.',
    ],

    // Symbole
    'symbol' => [
        'required' => 'Le symbole est obligatoire.',
        'string' => 'Le symbole doit être une chaîne de caractères.',
        'max' => 'Le symbole ne doit pas dépasser 10 caractères.',
    ],

    // Décimales
    'decimal_places' => [
        'required' => 'Le nombre de décimales est obligatoire.',
        'integer' => 'Le nombre de décimales doit être un entier.',
        'between' => 'Le nombre de décimales doit être compris entre 0 et 4.',
    ],

    // Code numérique
    'numeric_code' => [
        'string' => 'Le code numérique doit être une chaîne de caractères.',
        'size' => 'Le code numérique doit contenir exactement 3 chiffres.',
        'regex' => 'Le code numérique doit contenir uniquement des chiffres (ex: 978, 840, 952).',
        'unique' => 'Ce code numérique est déjà utilisé.',
    ],

    // Ordre d'affichage
    'sort_order' => [
        'required' => 'L\'ordre d\'affichage est obligatoire.',
        'integer' => 'L\'ordre d\'affichage doit être un entier.',
        'min' => 'L\'ordre d\'affichage doit être supérieur ou égal à 0.',
        'unique' => 'Cet ordre d\'affichage est déjà utilisé par une autre devise.',
    ],

    // Taux de change (Module 10 : prix multi-devise)
    'exchange_rate' => [
        'required' => 'Le taux de change est obligatoire.',
        'numeric' => 'Le taux de change doit être un nombre.',
        'min' => 'Le taux de change doit être strictement supérieur à zéro.',
    ],

    // Devise par défaut
    'is_default' => [
        'boolean' => 'Le champ devise par défaut doit être un booléen.',
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

        // Nom
        'name' => [
            'required' => 'Le nom de la devise est obligatoire.',
            'string' => 'Le nom de la devise doit être une chaîne de caractères.',
            'max' => 'Le nom de la devise ne peut pas dépasser 100 caractères.',
        ],

        // Slug
        'slug' => [
            'string' => 'Le slug doit être une chaîne de caractères.',
            'max' => 'Le slug ne peut pas dépasser 255 caractères.',
        ],

        // Description
        'description' => [
            'string' => 'La description doit être une chaîne de caractères.',
        ],
    ],

];
