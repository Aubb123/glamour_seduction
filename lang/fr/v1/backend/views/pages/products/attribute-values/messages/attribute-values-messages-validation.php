<?php

return [
    'productAttributeValues' => [
        'attribute_id' => [
            'required' => 'L\'attribut parent est obligatoire.',
            'integer' => 'L\'attribut parent doit être un identifiant valide.',
            'exists' => 'L\'attribut parent sélectionné n\'existe pas.',
        ],
        'value' => [
            'required' => 'La clé machine de la valeur est obligatoire.',
            'string' => 'La clé machine de la valeur doit être une chaîne de caractères.',
            'max' => 'La clé machine de la valeur ne peut pas dépasser 100 caractères.',
            'regex' => 'La clé machine de la valeur doit être en minuscules, sans espaces (ex : rouge, xl, silicone-medical).',
        ],
        'sort_order' => [
            'required' => 'L\'ordre de tri est obligatoire.',
            'integer' => 'L\'ordre de tri doit être un nombre entier.',
            'min' => 'L\'ordre de tri doit être au minimum 0.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'translations' => [
            'required' => 'Les traductions sont obligatoires.',
            'array' => 'Les traductions doivent être un tableau.',
            'lang_id' => [
                'required' => 'La langue est requise pour chaque traduction.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],
            'label' => [
                'required' => 'Le libellé est requis pour chaque traduction.',
                'string' => 'Le libellé doit être une chaîne de caractères.',
                'max' => 'Le libellé ne peut pas dépasser 255 caractères.',
                'unique' => 'Le libellé doit être unique pour chaque langue.',
            ],
            'slug' => [
                'required' => 'Le slug est requis pour chaque traduction.',
                'string' => 'Le slug doit être une chaîne de caractères.',
                'max' => 'Le slug ne peut pas dépasser 255 caractères.',
                'unique' => 'Le slug doit être unique pour chaque langue.',
            ],
        ],
        'not_found' => 'La valeur d\'attribut de produits n\'a pas été trouvée.',
    ],
];
