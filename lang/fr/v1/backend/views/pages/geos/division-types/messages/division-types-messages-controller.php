<?php

return [

    'divisionTypes' => [

        'not_found' => 'Tentative d\'accès à un type de division introuvable (UUID: :uuid).',

        // ─────────────────────────────────────────────
        // INDEX
        // ─────────────────────────────────────────────
        'index' => [
            'success' => 'Liste des types de division consultée.',
            'error' => 'Erreur lors de la récupération de la liste des types de division.',
        ],

        // ─────────────────────────────────────────────
        // SHOW
        // ─────────────────────────────────────────────
        'show' => [
            'success' => 'Détails du type de division « :name » consultés.',
            'warning' => 'Tentative de consultation d\'un type de division inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de la récupération des détails du type de division (UUID: :uuid).',
            'slug_mismatch' => 'Incohérence de slug pour le type de division',
            'no_name' => 'Pas de nom',
        ],

        // ─────────────────────────────────────────────
        // CREATE
        // ─────────────────────────────────────────────
        'create' => [
            'success' => 'Accès à la page de création du type de division.',
            'warning' => 'Tentative de création d\'un type de division inexistante.',
            'error' => 'Erreur lors de l\'accès à la page de création du type de division.',
        ],

        // ─────────────────────────────────────────────
        // STORE
        // ─────────────────────────────────────────────
        'store' => [
            'success' => 'Le type de division « :name » a été créé avec succès.',
            'warning' => 'Erreur métier lors de la création du type de division.',
            'error' => 'Erreur lors de la création du type de division.',
            'flash_success' => 'Le type de division « :name » a été créé avec succès.',
            'flash_error' => 'Une erreur est survenue lors de la création du type de division. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // EDIT
        // ─────────────────────────────────────────────
        'edit' => [
            'success' => 'Accès à la page de modification du type de division « :name ».',
            'warning' => 'Tentative de modification d\'un type de division inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de l\'accès à la page de modification du type de division (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // UPDATE
        // ─────────────────────────────────────────────
        'update' => [
            'success' => 'Le type de division « :name » a été mis à jour avec succès.',
            'warning' => 'Tentative de mise à jour d\'un type de division inexistant (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la mise à jour du type de division (UUID: :uuid).',
            'error' => 'Erreur lors de la mise à jour du type de division (UUID: :uuid).',
            'flash_success' => 'Le type de division « :name » a été mis à jour avec succès.',
            'flash_warning' => 'Type de division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la mise à jour du type de division. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'success' => 'Le type de division « :name » a été activé avec succès.',
            'warning' => 'Tentative d\'activation d\'un type de division inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de l\'activation du type de division (UUID: :uuid).',
            'flash_success' => 'Le type de division « :name » a été activé avec succès.',
            'flash_warning' => 'Type de division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de l\'activation du type de division. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de l\'activation du type de division (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'success' => 'Le type de division « :name » a été désactivé avec succès.',
            'warning' => 'Tentative de désactivation d\'un type de division inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de la désactivation du type de division (UUID: :uuid).',
            'flash_success' => 'Le type de division « :name » a été désactivé avec succès.',
            'flash_warning' => 'Type de division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la désactivation du type de division. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de la désactivation du type de division (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DESTROY
        // ─────────────────────────────────────────────
        'destroy' => [
            'success' => 'Le type de division a été supprimé avec succès.',
            'warning' => 'Tentative de suppression d\'un type de division inexistant (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la suppression du type de division (UUID: :uuid).',
            'error' => 'Erreur lors de la suppression du type de division (UUID: :uuid).',
            'flash_success' => 'Le type de division a été supprimé avec succès.',
            'flash_warning' => 'Type de division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression du type de division. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // TRASHED
        // ─────────────────────────────────────────────
        'trashed' => [
            'success' => 'Liste des types de division supprimés récupérée avec succès.',
            'error' => 'Une erreur est survenue lors de la récupération de la liste des types de division supprimés.',
        ],

        // ─────────────────────────────────────────────
        // RESTORE
        // ─────────────────────────────────────────────
        'restore' => [
            'success' => 'Le type de division a été restauré avec succès.',
            'warning' => 'Tentative de restauration d\'un type de division inexistant (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la restauration du type de division (UUID: :uuid).',
            'not_found' => 'Type de division introuvable.',
            'error' => 'Erreur lors de la restauration du type de division (UUID: :uuid).',
            'flash_success' => 'Le type de division a été restauré avec succès.',
            'flash_warning' => 'Type de division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la restauration du type de division. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // FORCE DESTROY
        // ─────────────────────────────────────────────
        'force_destroy' => [
            'success' => 'Le type de division a été supprimé définitivement avec succès.',
            'warning' => 'Tentative de suppression définitive d\'un type de division inexistant (UUID: :uuid).',
            'error' => 'Erreur lors de la suppression définitive du type de division (UUID: :uuid).',
            'not_found' => 'Type de division introuvable.',
            'flash_success' => 'Le type de division a été supprimé définitivement avec succès.',
            'flash_warning' => 'Type de division introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression définitive du type de division. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de la suppression définitive du type de division (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // AUDITS
        // ─────────────────────────────────────────────
        'audits' => [
            'success' => 'Consultation des audits du type de division :name',
            'error' => 'Erreur lors de la consultation des audits du type de division : :uuid',
        ],

        // ─────────────────────────────────────────────
        // ACTIVITY LOGS
        // ─────────────────────────────────────────────
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité du type de division :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité du type de division : :uuid',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────
        'translations' => [

            'not_found' => 'Tentative d\'accès à une traduction de type de division introuvable (UUID: :uuid).',

            'destroy' => [
                'success' => 'La traduction du type de division a été supprimée avec succès.',
                'warning' => 'Tentative de suppression d\'une traduction de type de division inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la suppression de la traduction du type de division (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression de la traduction du type de division (UUID: :uuid).',
                'flash_success' => 'La traduction du type de division a été supprimée avec succès.',
                'flash_warning' => 'Traduction du type de division introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression de la traduction du type de division. Veuillez réessayer.',
            ],

            'trashed' => [
                'accessed' => 'Accès à la corbeille des traductions du type de division « :name ».',
                'not_found' => 'Type de division introuvable. Impossible d\'accéder à la corbeille des traductions (UUID: :uuid).',
                'error' => 'Une erreur est survenue lors de l\'accès à la corbeille des traductions du type de division (UUID: :uuid). Veuillez réessayer.',
            ],

            'restore' => [
                'success' => 'La traduction du type de division a été restaurée avec succès.',
                'warning' => 'Tentative de restauration d\'une traduction de type de division inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la restauration de la traduction du type de division (UUID: :uuid).',
                'not_found' => 'Traduction du type de division introuvable.',
                'error' => 'Erreur lors de la restauration de la traduction du type de division (UUID: :uuid).',
                'flash_success' => 'La traduction du type de division a été restaurée avec succès.',
                'flash_warning' => 'Traduction du type de division introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la restauration de la traduction du type de division. Veuillez réessayer.',
            ],

            'force_destroy' => [
                'success' => 'La traduction du type de division a été supprimée définitivement avec succès.',
                'warning' => 'Tentative de suppression définitive d\'une traduction de type de division inexistante (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression définitive de la traduction du type de division (UUID: :uuid).',
                'not_found' => 'Traduction du type de division introuvable.',
                'flash_success' => 'La traduction du type de division a été supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction du type de division introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la traduction du type de division. Veuillez réessayer.',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction du type de division (UUID: :uuid).',
            ],
        ],

    ],

];
