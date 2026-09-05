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
