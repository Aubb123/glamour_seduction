<?php

return [
    'users' => [
        'email' => [
            'prohibited' => 'L\'adresse e-mail ne peut pas être modifiée.',
        ],
        'first_name' => [
            'required' => 'Le prénom est obligatoire.',
            'string' => 'Le prénom doit être une chaîne de caractères.',
            'max' => 'Le prénom ne peut pas dépasser 255 caractères.',
        ],
        'last_name' => [
            'required' => 'Le nom est obligatoire.',
            'string' => 'Le nom doit être une chaîne de caractères.',
            'max' => 'Le nom ne peut pas dépasser 255 caractères.',
        ],
        'gender' => [
            'required' => 'Le genre est obligatoire.',
            'in' => 'Le genre doit être masculin ou féminin.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné est invalide.',
        ],
        'country_id' => [
            'required' => 'Le pays est obligatoire.',
            'exists' => 'Le pays sélectionné n\'existe pas.',
        ],
        'password' => [
            'string' => 'Le mot de passe doit être une chaîne de caractères.',
            'min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ],
        'roles' => [
            'required' => 'Les rôles sont obligatoires.',
            'array' => 'Les rôles doivent être un tableau.',
            'exists' => 'Un rôle sélectionné n\'existe pas.',
        ],
        'cloud_platform_uuid' => [
            'string' => 'L\'UUID de la plateforme cloud doit être une chaîne de caractères.',
            'exists' => 'La plateforme cloud sélectionnée n\'existe pas.',
        ],
        'image' => [
            'image' => 'L\'image doit être un fichier image valide.',
            'mimes' => 'L\'image doit être un fichier de type: jpg, png, webp.',
            'max' => 'L\'image ne peut pas dépasser 2 Mo.',
        ],
    ],
];
