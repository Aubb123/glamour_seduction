<?php

return [
    'galleries' => [
        'not_found' => 'Galerie introuvable.',

        // ── Champs de base ──
        'sort_order' => [
            'required' => 'L\'ordre de tri est obligatoire.',
            'integer' => 'L\'ordre de tri doit être un nombre entier.',
            'min' => 'L\'ordre de tri doit être au minimum :min.',
            'unique' => 'Cet ordre de tri est déjà utilisé par une autre galerie.',
        ],

        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],

        'cloud_platform_uuid' => [
            'required' => 'La plateforme cloud est obligatoire.',
            'exists' => 'La plateforme cloud sélectionnée n\'existe pas.',
        ],

        'image' => [
            'required' => 'L\'image est obligatoire.',
            'image' => 'Le fichier doit être une image.',
            'mimes' => 'L\'image doit être au format :values.',
            'max' => 'L\'image ne peut pas dépasser :max kilo-octets.',
        ],

        // ── Traductions ──
        'translations' => [
            'required' => 'Au moins une traduction est requise.',
            'array' => 'Les traductions doivent être un tableau.',

            'lang_id' => [
                'required' => 'La langue est obligatoire.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],

            'title' => [
                'required' => 'Le titre est obligatoire.',
                'string' => 'Le titre doit être une chaîne de caractères.',
                'max' => 'Le titre ne peut pas dépasser :max caractères.',
            ],

            'description' => [
                'required' => 'La description est obligatoire.',
                'string' => 'La description doit être une chaîne de caractères.',
            ],

            'unique_title' => 'Ce titre existe déjà pour cette langue.',
        ],
    ],
];
