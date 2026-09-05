<?php

return [
    'faqs' => [
        'sort_order' => [
            'required' => 'L\'ordre d\'affichage est obligatoire.',
            'integer' => 'L\'ordre d\'affichage doit être un nombre entier.',
            'min' => 'L\'ordre d\'affichage doit être au minimum 0.',
            'unique' => 'Cet ordre d\'affichage est déjà utilisé.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'translations' => [
            'required' => 'Les traductions sont obligatoires.',
            'array' => 'Les traductions doivent être un tableau.',
            'min' => 'Au moins une traduction est requise.',
            'lang_id' => [
                'required' => 'La langue est requise pour chaque traduction.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],
            'question' => [
                'required' => 'La question est requise pour chaque traduction.',
                'string' => 'La question doit être une chaîne de caractères.',
                'max' => 'La question ne peut pas dépasser 255 caractères.',
            ],
            'answer' => [
                'required' => 'La réponse est requise pour chaque traduction.',
                'string' => 'La réponse doit être une chaîne de caractères.',
            ],
            'unique_question' => 'Cette question est déjà utilisée pour cette langue.',
        ],
        'not_found' => 'La FAQ n\'a pas été trouvée.',
    ],
];
