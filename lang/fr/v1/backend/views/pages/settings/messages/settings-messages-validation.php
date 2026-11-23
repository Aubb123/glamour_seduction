<?php

return [
    'settings' => [
        // ── Tableau settings ──
        'settings' => [
            'required' => 'Au moins un paramètre est obligatoire.',
            'array' => 'Les paramètres doivent être un tableau.',
            'min' => 'Au moins un paramètre est obligatoire.',
        ],

        // ── UUID ──
        'uuid' => [
            'required' => 'L\'UUID du paramètre est obligatoire.',
            'exists' => 'Le paramètre sélectionné n\'existe pas.',
        ],

        // ── Valeur ──
        'value' => [
            'string' => 'La valeur doit être une chaîne de caractères.',
            'integer' => 'La valeur du paramètre « :key » doit être un entier.',
            'numeric' => 'La valeur du paramètre « :key » doit être un nombre.',
            'range' => 'La valeur du paramètre « :key » doit être comprise entre :min et :max.',
            'image_range' => "Le nombre minimum d'images par produit ne peut pas dépasser le nombre maximum.",
        ],

        // ── Traductions ──
        'translations' => [
            'lang_id' => [
                'required' => 'La langue de la traduction est obligatoire.',
                'integer' => 'L\'identifiant de la langue doit être un entier.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],
            'value' => [
                'string' => 'La valeur traduite doit être une chaîne de caractères.',
            ],
        ],
    ],
];
