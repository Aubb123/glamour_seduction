<?php

return [
    'code' => [
        'required' => 'Le code est obligatoire.',
        'string' => 'Le code doit être une chaîne de caractères.',
        'max' => 'Le code ne peut pas dépasser 10 caractères.',
        'unique' => 'Ce code est déjà utilisé.',
        'regex' => 'Le code ne peut contenir que des lettres minuscules, chiffres et tirets.',
    ],
    'status' => [
        'required' => 'Le statut est obligatoire.',
        'in' => 'Le statut sélectionné n\'est pas valide.',
    ],
    'released_at' => [
        'date' => 'La date de lancement doit être une date valide.',
        'after_or_equal' => 'La date de lancement doit être aujourd\'hui ou une date future.',
    ],
    'translations' => [
        'required' => 'Les traductions sont obligatoires.',
        'array' => 'Le format des traductions est invalide.',
        'min' => 'Au moins une traduction est requise.',
        'lang_id' => [
            'required' => 'La langue est obligatoire pour chaque traduction.',
            'integer' => 'L\'ID de la langue doit être un entier.',
            'exists' => 'La langue sélectionnée est invalide.',
            'duplicate' => 'Une langue ne peut pas apparaître plusieurs fois dans les traductions.',
        ],
        'name' => [
            'required' => 'Le nom est obligatoire pour chaque traduction.',
            'string' => 'Le nom doit être une chaîne de caractères.',
            'max' => 'Le nom ne peut pas dépasser 255 caractères.',
        ],
        'description' => [
            'required' => 'La description est obligatoire pour chaque traduction.',
            'string' => 'La description doit être une chaîne de caractères.',
        ],
    ],
];
