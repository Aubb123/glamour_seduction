<?php

return [

    'countries' => [

        'not_found' => 'Tentative d\'accès à un pays introuvable (UUID: :uuid).',

        // ─────────────────────────────────────────────
        // INDEX
        // ─────────────────────────────────────────────
        'index' => [
            'success' => 'Liste des pays consultée.',
            'error' => 'Erreur lors de la récupération de la liste des pays.',
        ],

        // ─────────────────────────────────────────────
        // SHOW
        // ─────────────────────────────────────────────
        'show' => [
            'success' => 'Détails du pays ":name" consultés.',
            'warning' => 'Tentative de consultation d\'un pays inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de la récupération des détails du pays (UUID: :uuid).',
            'no_name' => 'Pas de nom',
            'slug_mismatch' => 'Incohérence de slug pour le pays',
        ],

        // ─────────────────────────────────────────────
        // USERS
        // ─────────────────────────────────────────────
        'users' => [
            'slug_mismatch' => 'Incohérence de slug pour le pays',
            'success' => 'Consultation des utilisateurs du pays : :name',
            'warning' => 'Tentative de consultation des utilisateurs d\'un pays inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des utilisateurs du pays : :uuid',
        ],

        // ─────────────────────────────────────────────
        // EDIT
        // ─────────────────────────────────────────────
        'edit' => [
            'success' => 'Accès à la page de modification du pays ":name".',
            'warning' => 'Tentative de modification d\'un pays inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de l\'accès à la page de modification du pays (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // UPDATE
        // ─────────────────────────────────────────────
        'update' => [
            'success' => 'Le pays ":name" a été mis à jour avec succès.',
            'warning' => 'Tentative de mise à jour d\'un pays inexistant (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la mise à jour du pays (UUID: :uuid).',
            'error' => 'Erreur lors de la mise à jour du pays (UUID: :uuid).',
            'flash_success' => 'Le pays ":name" a été mis à jour avec succès.',
            'flash_warning' => 'Pays introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la mise à jour du pays. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'success' => 'Le pays ":name" a été activé avec succès.',
            'warning' => 'Tentative d\'activation d\'un pays inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de l\'activation du pays (UUID: :uuid).',
            'flash_success' => 'Le pays ":name" a été activé avec succès.',
            'flash_warning' => 'Pays introuvable.',
            'flash_error' => 'Une erreur est survenue lors de l\'activation du pays. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de l\'activation du pays (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'success' => 'Le pays ":name" a été désactivé avec succès.',
            'warning' => 'Tentative de désactivation d\'un pays inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de la désactivation du pays (UUID: :uuid).',
            'flash_success' => 'Le pays ":name" a été désactivé avec succès.',
            'flash_warning' => 'Pays introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la désactivation du pays. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de la désactivation du pays (UUID: :uuid).',
        ],
        'trashed' => [
            'success' => 'Liste des pays supprimés récupérée avec succès.',
            'error' => 'Une erreur est survenue lors de la récupération de la liste des pays supprimés.',
        ],
        'audits' => [
            'slug_mismatch' => 'Incohérence de slug pour le pays',
            'success' => 'Consultation des audits du pays :name',
            'error' => 'Erreur lors de la consultation des audits du pays : :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Incohérence de slug pour le pays',
            'success' => 'Consultation des journaux d\'activité du pays :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité du pays : :uuid',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        // ─────────────────────────────────────────────
        // TRANSLATIONS DESTROY
        // ─────────────────────────────────────────────
        'translations' => [
            'not_found' => 'Tentative d\'accès à une traduction de pays introuvable (UUID: :uuid).',
            'destroy' => [
                'success' => 'La traduction du pays a été supprimée avec succès.',
                'warning' => 'Tentative de suppression d\'une traduction de pays inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la suppression de la traduction du pays (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression de la traduction du pays (UUID: :uuid).',
                'flash_success' => 'La traduction du pays a été supprimée avec succès.',
                'flash_warning' => 'Traduction du pays introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression de la traduction du pays. Veuillez réessayer.',
            ],
            'trashed' => [
                'accessed' => 'Accès à la corbeille des traductions du pays ":name".',
                'not_found' => 'Pays introuvable. Impossible d\'accéder à la corbeille des traductions (UUID: :uuid).',
                'error' => 'Une erreur est survenue lors de l\'accès à la corbeille des traductions du pays (UUID: :uuid). Veuillez réessayer.',
            ],
            'restore' => [
                'success' => 'La traduction du pays a été restaurée avec succès.',
                'warning' => 'Tentative de restauration d\'une traduction de pays inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la restauration de la traduction du pays (UUID: :uuid).',
                'error' => 'Erreur lors de la restauration de la traduction du pays (UUID: :uuid).',
                'flash_success' => 'La traduction du pays a été restaurée avec succès.',
                'flash_warning' => 'Traduction du pays introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la restauration de la traduction du pays. Veuillez réessayer.',
            ],
            'force_destroy' => [
                'success' => 'La traduction du pays a été supprimée définitivement avec succès.',
                'warning' => 'Tentative de suppression définitive d\'une traduction de pays inexistante (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression définitive de la traduction du pays (UUID: :uuid).',
                'flash_success' => 'La traduction du pays a été supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction du pays introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la traduction du pays. Veuillez réessayer.',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction du pays (UUID: :uuid).',
            ],
        ],

    ],

];
