<?php

return [
    'positions' => [
        'code' => ['required' => 'Le code est obligatoire.', 'string' => 'Le code doit être une chaîne de caractères.', 'max' => 'Le code ne peut pas dépasser 30 caractères.', 'unique' => 'Le code doit être unique.'],
        'context' => ['required' => 'La portée est obligatoire.', 'in' => 'La portée sélectionnée n\'est pas valide.'],
        'status' => ['required' => 'Le statut est obligatoire.', 'in' => 'Le statut sélectionné n\'est pas valide.'],
        'sort_order' => ['required' => 'L\'ordre d\'affichage est obligatoire.', 'integer' => 'L\'ordre d\'affichage doit être un entier.', 'min' => 'L\'ordre d\'affichage ne peut pas être négatif.'],
        'translations' => [
            'required' => 'Au moins une traduction est obligatoire.',
            'array' => 'Les traductions doivent être une liste.',
            'lang_id' => ['required' => 'La langue est obligatoire.', 'exists' => 'La langue sélectionnée est introuvable.'],
            'name' => ['required' => 'Le nom est obligatoire.', 'string' => 'Le nom doit être une chaîne de caractères.', 'max' => 'Le nom ne peut pas dépasser 255 caractères.', 'unique' => 'Ce nom est déjà utilisé pour un autre poste dans cette langue.'],
            'description' => ['string' => 'La description doit être une chaîne de caractères.', 'max' => 'La description ne peut pas dépasser 1000 caractères.'],
        ],
    ],
];
