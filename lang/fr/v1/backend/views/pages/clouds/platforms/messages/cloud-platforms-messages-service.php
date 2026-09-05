<?php

return [
    'cloud-platforms' => [
        'store' => [
            'invalid_name' => 'Le nom de la plateforme cloud est invalide. Les noms valides sont : backblaze_b2.',
        ],
        'update' => [
            'invalid_name' => 'Le nom de la plateforme cloud est invalide. Les noms valides sont : backblaze_b2.',
        ],
        'destroy' => [
            'already_deleted' => 'Cette plateforme est déjà supprimée et ne peut plus être supprimée à nouveau.',
            'has_folders' => 'Impossible de supprimer cette plateforme car elle contient des dossiers. Veuillez d\'abord supprimer les dossiers associés.',
            'status_active_forbidden' => 'Impossible de supprimer cette plateforme car elle est actuellement active. Veuillez d\'abord la désactiver.',
        ],
        'force_destroy' => [
            'has_folders' => 'Impossible de supprimer cette plateforme car elle contient des dossiers. Veuillez d\'abord supprimer les dossiers associés.',
            'status_active_forbidden' => 'Impossible de supprimer cette plateforme car elle est actuellement active. Veuillez d\'abord la désactiver.',
            'not_trashed_forbidden' => 'Seules les plateformes déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les plateformes déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'architecture_store' => [
            'success' => 'Architecture cloud créée pour la plateforme : :name',
            'error' => 'Erreur lors de la création de l\'architecture cloud pour : :name',
            'inactive_platform' => 'La plateforme cloud :name est inactive. Veuillez l\'activer avant de créer l\'architecture.',
        ],
    ],
];
