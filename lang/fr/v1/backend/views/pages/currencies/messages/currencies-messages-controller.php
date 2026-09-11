<?php

return [

    'currencies' => [

        'not_found' => 'Tentative d\'accès à une devise introuvable (UUID: :uuid).',

        // ─────────────────────────────────────────────
        // INDEX
        // ─────────────────────────────────────────────
        'index' => [
            'success' => 'Liste des devises consultée.',
            'error' => 'Erreur lors de la récupération de la liste des devises.',
        ],

        // ─────────────────────────────────────────────
        // SHOW
        // ─────────────────────────────────────────────
        'show' => [
            'success' => 'Détails de la devise ":name" consultés.',
            'warning' => 'Tentative de consultation d\'une devise inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de la récupération des détails de la devise (UUID: :uuid).',
            'no_name' => 'Pas de nom',
            'slug_mismatch' => 'Incohérence de slug pour la devise',
        ],

        // ─────────────────────────────────────────────
        // COUNTRIES
        // ─────────────────────────────────────────────
        'countries' => [
            'slug_mismatch' => 'Incohérence de slug pour la devise',
            'success' => 'Consultation des pays utilisant la devise : :name',
            'warning' => 'Tentative de consultation des pays d\'une devise inexistante : :uuid',
            'error' => 'Erreur lors de la consultation des pays de la devise : :uuid',
        ],

        // ─────────────────────────────────────────────
        // AUDITS
        // ─────────────────────────────────────────────
        'audits' => [
            'slug_mismatch' => 'Incohérence de slug pour la devise',
            'success' => 'Consultation des audits de la devise :name',
            'error' => 'Erreur lors de la consultation des audits de la devise : :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Incohérence de slug pour la devise',
            'success' => 'Consultation des journaux d\'activité de la devise :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité de la devise : :uuid',
        ],

        // ─────────────────────────────────────────────
        // EDIT
        // ─────────────────────────────────────────────
        'edit' => [
            'success' => 'Accès à la page de modification de la devise ":name".',
            'warning' => 'Tentative de modification d\'une devise inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de l\'accès à la page de modification de la devise (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // UPDATE
        // ─────────────────────────────────────────────
        'update' => [
            'success' => 'La devise ":name" a été mise à jour avec succès.',
            'exchange_rate_changed' => 'Taux de change de la devise " :code" mis à jour : :rate',
            'warning' => 'Tentative de mise à jour d\'une devise inexistante (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la mise à jour de la devise (UUID: :uuid).',
            'error' => 'Erreur lors de la mise à jour de la devise (UUID: :uuid).',
            'flash_success' => 'La devise ":name" a été mise à jour avec succès.',
            'flash_warning' => 'Devise introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la mise à jour de la devise. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'success' => 'La devise ":name" a été activée avec succès.',
            'warning' => 'Tentative d\'activation d\'une devise inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de l\'activation de la devise (UUID: :uuid).',
            'flash_success' => 'La devise ":name" a été activée avec succès.',
            'flash_warning' => 'Devise introuvable.',
            'flash_error' => 'Une erreur est survenue lors de l\'activation de la devise. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de l\'activation de la devise (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'success' => 'La devise ":name" a été désactivée avec succès.',
            'warning' => 'Tentative de désactivation d\'une devise inexistante (UUID: :uuid).',
            'error' => 'Erreur lors de la désactivation de la devise (UUID: :uuid).',
            'flash_success' => 'La devise ":name" a été désactivée avec succès.',
            'flash_warning' => 'Devise introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la désactivation de la devise. Veuillez réessayer.',
            'warning_meta' => 'Erreur métier lors de la désactivation de la devise (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DESTROY
        // ─────────────────────────────────────────────
        'destroy' => [
            'success' => 'La devise ":name" a été supprimée avec succès.',
            'warning' => 'Tentative de suppression d\'une devise inexistante (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la suppression de la devise (UUID: :uuid).',
            'error' => 'Erreur lors de la suppression de la devise (UUID: :uuid).',
            'flash_success' => 'La devise ":name" a été supprimée avec succès.',
            'flash_warning' => 'Devise introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression de la devise. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // TRASHED
        // ─────────────────────────────────────────────
        'trashed' => [
            'success' => 'Liste des devises supprimées consultée.',
            'error' => 'Erreur lors de la récupération de la liste des devises supprimées.',
        ],

        // ─────────────────────────────────────────────
        // RESTORE
        // ─────────────────────────────────────────────
        'restore' => [
            'success' => 'La devise ":name" a été restaurée avec succès.',
            'warning' => 'Tentative de restauration d\'une devise inexistante (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la restauration de la devise (UUID: :uuid).',
            'error' => 'Erreur lors de la restauration de la devise (UUID: :uuid).',
            'flash_success' => 'La devise ":name" a été restaurée avec succès.',
            'flash_warning' => 'Devise introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la restauration de la devise. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // FORCE DESTROY
        // ─────────────────────────────────────────────
        'force_destroy' => [
            'success' => 'La devise ":name" a été supprimée définitivement avec succès.',
            'warning' => 'Tentative de suppression définitive d\'une devise inexistante (UUID: :uuid).',
            'warning_meta' => 'Erreur métier lors de la suppression définitive de la devise (UUID: :uuid).',
            'error' => 'Erreur lors de la suppression définitive de la devise (UUID: :uuid).',
            'flash_success' => 'La devise ":name" a été supprimée définitivement avec succès.',
            'flash_warning' => 'Devise introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la devise. Veuillez réessayer.',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────
        'translations' => [
            'not_found' => 'Tentative d\'accès à une traduction de devise introuvable (UUID: :uuid).',
            'destroy' => [
                'success' => 'La traduction de la devise a été supprimée avec succès.',
                'warning' => 'Tentative de suppression d\'une traduction de devise inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la suppression de la traduction de la devise (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression de la traduction de la devise (UUID: :uuid).',
                'flash_success' => 'La traduction de la devise a été supprimée avec succès.',
                'flash_warning' => 'Traduction de la devise introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression de la traduction de la devise. Veuillez réessayer.',
            ],
            'trashed' => [
                'success' => 'Accès à la corbeille des traductions de la devise ":name".',
                'not_found' => 'Devise introuvable. Impossible d\'accéder à la corbeille des traductions (UUID: :uuid).',
                'error' => 'Une erreur est survenue lors de l\'accès à la corbeille des traductions de la devise (UUID: :uuid). Veuillez réessayer.',
            ],
            'restore' => [
                'success' => 'La traduction de la devise a été restaurée avec succès.',
                'warning' => 'Tentative de restauration d\'une traduction de devise inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la restauration de la traduction de la devise (UUID: :uuid).',
                'error' => 'Erreur lors de la restauration de la traduction de la devise (UUID: :uuid).',
                'flash_success' => 'La traduction de la devise a été restaurée avec succès.',
                'flash_warning' => 'Traduction de la devise introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la restauration de la traduction de la devise. Veuillez réessayer.',
            ],
            'force_destroy' => [
                'success' => 'La traduction de la devise a été supprimée définitivement avec succès.',
                'warning' => 'Tentative de suppression définitive d\'une traduction de devise inexistante (UUID: :uuid).',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction de la devise (UUID: :uuid).',
                'error' => 'Erreur lors de la suppression définitive de la traduction de la devise (UUID: :uuid).',
                'flash_success' => 'La traduction de la devise a été supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction de la devise introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la traduction de la devise. Veuillez réessayer.',
            ],
        ],

    ],

];
