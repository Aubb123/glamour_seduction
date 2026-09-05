<?php

return [
    'geocontinents' => [
        'not_found' => 'Tentative d\'accès à un continent introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Liste des continents récupérée avec succès.',
            'error' => 'Une erreur est survenue lors de la récupération de la liste des continents.',
        ],
        'show' => [
            'no_name' => 'Continent sans nom',
            'success' => 'Détail du continent :name récupéré avec succès.',
            'warning' => 'Continent introuvable (UUID: :uuid).',
            'error' => 'Une erreur est survenue lors de la récupération du continent (UUID: :uuid).',
            'slug_mismatch' => 'Incohérence du slug pour le continent',
        ],
        'countries' => [
            'slug_mismatch' => 'Incohérence du slug pour le continent',
            'success' => 'Consultation des pays du continent : :name',
            'error' => 'Erreur lors de la consultation des pays du continent : :uuid',
        ],
        'audits' => [
            'slug_mismatch' => 'Incohérence du slug pour le continent',
            'success' => 'Consultation des audits du continent :name',
            'error' => 'Erreur lors de la consultation des audits du continent : :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Incohérence du slug pour le continent',
            'success' => 'Consultation des journaux d\'activité du continent :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité du continent : :uuid',
        ],
        'activate' => [
            'success' => 'Continent :name activé avec succès.',
            'warning' => 'Continent introuvable (UUID: :uuid).',
            'error' => 'Une erreur est survenue lors de l\'activation du continent (UUID: :uuid).',
            'flash_success' => 'Le continent :name a été activé avec succès.',
            'flash_error' => 'Une erreur est survenue lors de l\'activation du continent.',
        ],
        'deactivate' => [
            'success' => 'Continent :name désactivé avec succès.',
            'warning' => 'Continent introuvable (UUID: :uuid).',
            'error' => 'Une erreur est survenue lors de la désactivation du continent (UUID: :uuid).',
            'flash_success' => 'Le continent :name a été désactivé avec succès.',
            'flash_error' => 'Une erreur est survenue lors de la désactivation du continent.',
        ],
        'trashed' => [
            'success' => 'Liste des continents supprimés récupérée avec succès.',
            'error' => 'Une erreur est survenue lors de la récupération de la liste des continents supprimés.',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification du continent : :name',
            'warning' => 'Tentative de modification d\'un continent introuvable : :uuid',
            'error' => 'Erreur lors de l\'accès à la page de modification du continent : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour du continent en base de données : :name',
            'warning' => 'Tentative de mise à jour d\'un continent introuvable : :uuid',
            'warning_meta' => 'Erreur métier lors de la mise à jour du continent : :uuid',
            'error' => 'Erreur lors de la mise à jour du continent : :uuid',
            'flash_success' => 'Continent (:name) mis à jour avec succès.',
            'flash_warning' => 'Continent introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'translations' => [
            'not_found' => 'Tentative d\'accès à une traduction de continent introuvable (UUID: :uuid).',
            'destroy' => [
                'success' => 'La traduction du continent a été supprimée avec succès.',
                'warning' => 'Tentative de suppression d\'une traduction de continent introuvable (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la suppression de la traduction du continent (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression de la traduction du continent (UUID: :uuid).',
                'flash_success' => 'La traduction du continent a été supprimée avec succès.',
                'flash_warning' => 'La traduction du continent est introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression de la traduction du continent. Veuillez réessayer.',
            ],
            'trashed' => [
                'success' => 'Accès à la corbeille des traductions du continent ":name".',
                'not_found' => 'Le continent est introuvable. Impossible d\'accéder à la corbeille des traductions (UUID: :uuid).',
                'error' => 'Une erreur est survenue lors de l\'accès à la corbeille des traductions du continent (UUID: :uuid). Veuillez réessayer.',
            ],
            'restore' => [
                'success' => 'La traduction du continent a été restaurée avec succès.',
                'warning' => 'Tentative de restauration d\'une traduction de continent introuvable (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la restauration de la traduction du continent (UUID: :uuid).',
                'error' => 'Erreur lors de la restauration de la traduction du continent (UUID: :uuid).',
                'flash_success' => 'La traduction du continent a été restaurée avec succès.',
                'flash_warning' => 'La traduction du continent est introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la restauration de la traduction du continent. Veuillez réessayer.',
            ],
            'force_destroy' => [
                'success' => 'La traduction du continent a été supprimée définitivement avec succès.',
                'warning' => 'Tentative de suppression définitive d\'une traduction de continent introuvable (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction du continent (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression définitive de la traduction du continent (UUID: :uuid).',
                'flash_success' => 'La traduction du continent a été supprimée définitivement avec succès.',
                'flash_warning' => 'La traduction du continent est introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la traduction du continent. Veuillez réessayer.',
            ],
        ],
    ],
];
