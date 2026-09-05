<?php

return [
    'tags' => [
        'not_found' => 'Tag de blog introuvable.',

        // ── Champs de base ──
        'sort_order' => [
            'required' => 'L\'ordre de tri est obligatoire.',
            'integer' => 'L\'ordre de tri doit être un nombre entier.',
            'min' => 'L\'ordre de tri doit être au minimum :min.',
            'unique' => 'Cet ordre de tri est déjà utilisé.',
        ],

        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],

        // ── Traductions ──
        'translations' => [
            'required' => 'Au moins une traduction est requise.',
            'array' => 'Les traductions doivent être un tableau.',

            'lang_id' => [
                'required' => 'La langue est obligatoire.',
                'distinct' => 'Une même langue ne peut pas être renseignée deux fois.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],

            'name' => [
                'required' => 'Le nom est obligatoire.',
                'string' => 'Le nom doit être une chaîne de caractères.',
                'max' => 'Le nom ne peut pas dépasser :max caractères.',
                'unique' => 'Ce nom de tag existe déjà pour cette langue.',
            ],
        ],
    ],
];
