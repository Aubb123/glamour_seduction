<?php

return [
    'warehouseLocations' => [
        'warehouse_id' => [
            'required' => 'L\'entrepôt de rattachement est obligatoire.',
            'integer' => 'L\'entrepôt doit être un identifiant valide.',
            'exists' => 'L\'entrepôt sélectionné n\'existe pas ou est en corbeille.',
        ],
        'parent_id' => [
            'integer' => 'L\'emplacement parent doit être un identifiant valide.',
            'exists' => 'L\'emplacement parent sélectionné n\'existe pas.',
            'trashed' => 'L\'emplacement parent sélectionné est en corbeille.',
            'same_warehouse' => 'L\'emplacement parent doit appartenir au même entrepôt.',
            'self' => 'Un emplacement ne peut pas être son propre parent.',
            'cycle' => 'Impossible de rattacher cet emplacement à l\'un de ses propres descendants (cycle).',
        ],
        'type' => [
            'required' => 'Le type de l\'emplacement est obligatoire.',
            'in' => 'Le type sélectionné n\'est pas valide.',
        ],
        'status' => [
            'required' => 'Le statut de l\'emplacement est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'code' => [
            'required' => 'Le code de l\'emplacement est obligatoire.',
            'string' => 'Le code doit être une chaîne de caractères.',
            'max' => 'Le code ne peut pas dépasser 30 caractères.',
            'regex' => 'Le code doit contenir uniquement des lettres majuscules, des chiffres, des tirets ou des underscores.',
            'unique' => 'Ce code est déjà utilisé par un autre emplacement de cet entrepôt (corbeille incluse).',
        ],
        'sort_order' => [
            'required' => 'L\'ordre de tri est obligatoire.',
            'integer' => 'L\'ordre de tri doit être un nombre entier.',
            'min' => 'L\'ordre de tri doit être au minimum 0.',
            'unique' => 'Cet ordre de tri est déjà utilisé par un autre emplacement de cet entrepôt (corbeille incluse).',
        ],
        'notes' => [
            'string' => 'Les notes doivent être une chaîne de caractères.',
            'max' => 'Les notes ne peuvent pas dépasser :max caractères.',
        ],
        'translations' => [
            'required' => 'Les traductions sont obligatoires.',
            'array' => 'Les traductions doivent être un tableau.',
            'lang_id' => [
                'required' => 'La langue est requise pour chaque traduction.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
                'duplicate' => 'Une langue ne peut pas apparaître plusieurs fois dans les traductions.',
            ],
            'name' => [
                'required' => 'Le nom est requis pour chaque traduction.',
                'string' => 'Le nom doit être une chaîne de caractères.',
                'max' => 'Le nom ne peut pas dépasser 150 caractères.',
                'unique_in_warehouse' => 'Ce nom est déjà utilisé par un autre emplacement de cet entrepôt pour cette langue (corbeille incluse).',
            ],
            'description' => [
                'required' => 'La description est obligatoire.',
                'string' => 'La description doit être une chaîne de caractères.',
            ],
        ],
        'not_found' => 'L\'emplacement n\'a pas été trouvé.',
    ],
];
