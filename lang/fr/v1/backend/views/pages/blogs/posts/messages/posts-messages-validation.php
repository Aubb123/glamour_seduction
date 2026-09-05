<?php

return [
    'posts' => [
        'category_id' => [
            'required' => 'La catégorie est obligatoire.',
            'exists' => 'La catégorie sélectionnée n\'existe pas.',
        ],
        'status' => [
            'required' => 'Le statut de l\'article est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'sort_order' => [
            'required' => 'L\'ordre de tri est obligatoire.',
            'integer' => 'L\'ordre de tri doit être un nombre entier.',
            'min' => 'L\'ordre de tri doit être au minimum 0.',
        ],
        'published_at' => [
            'date' => 'La date de publication doit être une date valide.',
            'required_if' => 'La date de publication est obligatoire lorsque le statut est "Programmé".',
            'after' => 'La date de publication programmée doit être une date future.',
        ],
        'tags' => [
            'required' => 'Les tags sont obligatoires.',
            'array' => 'Les tags doivent être un tableau.',
            'exists' => 'Un ou plusieurs tags sélectionnés n\'existent pas.',
        ],
        'translations' => [
            'required' => 'Les traductions sont obligatoires.',
            'array' => 'Les traductions doivent être un tableau.',
            'lang_id' => [
                'required' => 'La langue est requise pour chaque traduction.',
                'exists' => 'La langue sélectionnée n\'existe pas.',
            ],
            'title' => [
                'required' => 'Le titre est requis pour chaque traduction.',
                'string' => 'Le titre doit être une chaîne de caractères.',
                'max' => 'Le titre ne peut pas dépasser 255 caractères.',
            ],
            'content' => [
                'required' => 'Le contenu est requis pour chaque traduction.',
                'string' => 'Le contenu doit être une chaîne de caractères.',
            ],
            'unique_title' => 'Ce titre est déjà utilisé pour cette langue.',
        ],
        'not_found' => 'L\'article de blog n\'a pas été trouvé.',
    ],
];
