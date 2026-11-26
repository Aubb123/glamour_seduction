<?php

return [

    'divisions' => [

        'not_found' => 'Tentative d\'accès à une division introuvable (UUID: :uuid).',

        'index' => [
            'success' => 'Liste des divisions consultée.',
            'error' => 'Erreur lors de la récupération de la liste des divisions.',
        ],

        'show' => [
            'success' => 'Détails de la division « :name » consultés.',
            'warning' => 'Tentative de consultation d\'une division inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de la récupération des détails de la division (UUID: :uuid).',
            'slug_mismatch' => 'Incohérence de slug pour la division',
            'no_name' => 'Pas de nom',
        ],

        'create' => [
            'success' => 'Accès à la page de création de la division.',
            'warning' => 'Tentative de création d\'une division inexistante.',
            'error' => 'Erreur lors de l\'accès à la page de création de la division.',
        ],

        'store' => [
            'success' => 'La division « :name » a été créée avec succès.',
            'warning' => 'Erreur métier lors de la création de la division.',
            'error' => 'Erreur lors de la création de la division.',
            'flash_success' => 'La division « :name » a été créée avec succès.',
            'flash_error' => 'Une erreur est survenue lors de la création de la division. Veuillez réessayer.',
        ],

        'edit' => [
            'success' => 'Accès à la page de modification de la division « :name ».',
            'warning' => 'Tentative de modification d\'une division inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de l\'accès à la page de modification de la division (UUID: :uuid).',
        ],

        'update' => [
            'success' => 'La division « :name » a été mise à jour avec succès.',
            'warning' => 'Tentative de mise à jour d\'une division inexistante (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la mise à jour de la division (UUID: :uuid).',
            'error' => 'Erreur lors de la mise à jour de la division (UUID: :uuid).',
            'flash_success' => 'La division « :name » a été mise à jour avec succès.',
            'flash_warning' => 'Division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la mise à jour de la division. Veuillez réessayer.',
        ],

        'activate' => [
            'success' => 'La division « :name » a été activée avec succès.',
            'warning' => 'Tentative d\'activation d\'une division inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de l\'activation de la division (UUID: :uuid).',
            'flash_success' => 'La division « :name » a été activée avec succès.',
            'flash_warning' => 'Division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de l\'activation de la division. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de l\'activation de la division (UUID: :uuid).',
        ],

        'deactivate' => [
            'success' => 'La division « :name » a été désactivée avec succès.',
            'warning' => 'Tentative de désactivation d\'une division inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de la désactivation de la division (UUID: :uuid).',
            'flash_success' => 'La division « :name » a été désactivée avec succès.',
            'flash_warning' => 'Division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la désactivation de la division. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de la désactivation de la division (UUID: :uuid).',
        ],

        'destroy' => [
            'success' => 'La division a été supprimée avec succès.',
            'warning' => 'Tentative de suppression d\'une division inexistante (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la suppression de la division (UUID: :uuid).',
            'error' => 'Erreur lors de la suppression de la division (UUID: :uuid).',
            'flash_success' => 'La division a été supprimée avec succès.',
            'flash_warning' => 'Division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression de la division. Veuillez réessayer.',
        ],

        'trashed' => [
            'success' => 'Liste des divisions supprimées récupérée avec succès.',
            'error' => 'Une erreur est survenue lors de la récupération de la liste des divisions supprimées.',
        ],

        'restore' => [
            'success' => 'La division a été restaurée avec succès.',
            'warning' => 'Tentative de restauration d\'une division inexistante (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la restauration de la division (UUID: :uuid).',
            'not_found' => 'Division introuvable.',
            'error' => 'Erreur lors de la restauration de la division (UUID: :uuid).',
            'flash_success' => 'La division a été restaurée avec succès.',
            'flash_warning' => 'Division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la restauration de la division. Veuillez réessayer.',
        ],

        'force_destroy' => [
            'success' => 'La division a été supprimée définitivement avec succès.',
            'warning' => 'Tentative de suppression définitive d\'une division inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de la suppression définitive de la division (UUID: :uuid).',
            'not_found' => 'Division introuvable.',
            'flash_success' => 'La division a été supprimée définitivement avec succès.',
            'flash_warning' => 'Division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la division. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de la suppression définitive de la division (UUID: :uuid).',
        ],

        'audits' => [
            'success' => 'Consultation des audits de la division :name',
            'error' => 'Erreur lors de la consultation des audits de la division : :uuid',
        ],

        'activity_logs' => [
            'success' => 'Consultation des journaux d\'activité de la division :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité de la division : :uuid',
        ],

        'children' => [
            'success' => 'Consultation des sous-divisions de la division :name',
            'error' => 'Erreur lors de la consultation des sous-divisions de la division : :uuid',
            'slug_mismatch' => 'Incohérence de slug pour la division',
        ],

        'translations' => [

            'not_found' => 'Tentative d\'accès à une traduction de division introuvable (UUID: :uuid).',

            'destroy' => [
                'success' => 'La traduction de la division a été supprimée avec succès.',
                'warning' => 'Tentative de suppression d\'une traduction de division inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la suppression de la traduction de la division (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression de la traduction de la division (UUID: :uuid).',
                'flash_success' => 'La traduction de la division a été supprimée avec succès.',
                'flash_warning' => 'Traduction de la division introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression de la traduction de la division. Veuillez réessayer.',
            ],

            'trashed' => [
                'accessed' => 'Accès à la corbeille des traductions de la division « :name ».',
                'not_found' => 'Division introuvable. Impossible d\'accéder à la corbeille des traductions (UUID: :uuid).',
                'error' => 'Une erreur est survenue lors de l\'accès à la corbeille des traductions de la division (UUID: :uuid). Veuillez réessayer.',
            ],

            'restore' => [
                'success' => 'La traduction de la division a été restaurée avec succès.',
                'warning' => 'Tentative de restauration d\'une traduction de division inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la restauration de la traduction de la division (UUID: :uuid).',
                'not_found' => 'Traduction de la division introuvable.',
                'error' => 'Erreur lors de la restauration de la traduction de la division (UUID: :uuid).',
                'flash_success' => 'La traduction de la division a été restaurée avec succès.',
                'flash_warning' => 'Traduction de la division introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la restauration de la traduction de la division. Veuillez réessayer.',
            ],

            'force_destroy' => [
                'success' => 'La traduction de la division a été supprimée définitivement avec succès.',
                'warning' => 'Tentative de suppression définitive d\'une traduction de division inexistante (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression définitive de la traduction de la division (UUID: :uuid).',
                'not_found' => 'Traduction de la division introuvable.',
                'flash_success' => 'La traduction de la division a été supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction de la division introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la traduction de la division. Veuillez réessayer.',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction de la division (UUID: :uuid).',
            ],
        ],
    ],
];
