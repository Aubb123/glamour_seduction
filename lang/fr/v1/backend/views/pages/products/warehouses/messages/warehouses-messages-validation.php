<?php

return [
    'warehouses' => [
        'code' => [
            'required' => 'Le code de l\'entrepôt est obligatoire.',
            'string' => 'Le code doit être une chaîne de caractères.',
            'max' => 'Le code ne peut pas dépasser 30 caractères.',
            'regex' => 'Le code doit contenir uniquement des lettres majuscules, des chiffres, des tirets ou des underscores.',
            'unique' => 'Ce code est déjà utilisé par un autre entrepôt (corbeille incluse).',
        ],
        'sort_order' => [
            'required' => 'L\'ordre de tri est obligatoire.',
            'integer' => 'L\'ordre de tri doit être un nombre entier.',
            'min' => 'L\'ordre de tri doit être au minimum 0.',
            'unique' => 'Cet ordre de tri est déjà utilisé par un autre entrepôt.',
        ],
        'type' => [
            'required' => 'Le type de l\'entrepôt est obligatoire.',
            'in' => 'Le type sélectionné n\'est pas valide.',
        ],
        'status' => [
            'required' => 'Le statut de l\'entrepôt est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'is_default' => [
            'boolean' => 'La valeur « entrepôt par défaut » doit être un booléen.',
            'storage_required' => 'Un entrepôt par défaut doit obligatoirement être de type « stockage ».',
            'active_required' => 'Un entrepôt par défaut doit obligatoirement être actif.',
        ],
        'country_id' => [
            'integer' => 'Le pays doit être un identifiant valide.',
            'exists' => 'Le pays sélectionné n\'existe pas.',
        ],
        'city' => [
            'string' => 'La ville doit être une chaîne de caractères.',
            'max' => 'La ville ne peut pas dépasser 150 caractères.',
        ],
        'address' => [
            'string' => 'L\'adresse doit être une chaîne de caractères.',
            'max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
        ],
        'latitude' => [
            'numeric' => 'La latitude doit être un nombre.',
            'between' => 'La latitude doit être comprise entre -90 et 90.',
            'required_with' => 'La latitude est obligatoire lorsque la longitude est renseignée.',
        ],
        'longitude' => [
            'numeric' => 'La longitude doit être un nombre.',
            'between' => 'La longitude doit être comprise entre -180 et 180.',
            'required_with' => 'La longitude est obligatoire lorsque la latitude est renseignée.',
        ],
        'phone' => [
            'string' => 'Le téléphone doit être une chaîne de caractères.',
            'max' => 'Le téléphone ne peut pas dépasser 30 caractères.',
            'regex' => 'Le numéro de téléphone n\'est pas au format attendu (ex : +22997000001).',
        ],
        'email' => [
            'email' => 'L\'adresse email doit être valide.',
            'max' => 'L\'adresse email ne peut pas dépasser 150 caractères.',
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
                'unique' => 'Ce nom est déjà utilisé pour cette langue (corbeille incluse).',
            ],
            'description' => [
                'string' => 'La description doit être une chaîne de caractères.',
            ],
        ],
        'not_found' => 'L\'entrepôt n\'a pas été trouvé.',
    ],
];
