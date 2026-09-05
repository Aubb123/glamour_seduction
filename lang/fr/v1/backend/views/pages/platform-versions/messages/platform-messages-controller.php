<?php

return [
    'versions' => [
        'not_found' => 'Tentative d\'accès à une version introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Consultation de la liste des versions',
            'error' => 'Erreur lors de la consultation de la liste des versions',
        ],
        'create' => [
            'success' => 'Accès à la page de création d\'une version',
            'error' => 'Erreur lors de l\'accès à la page de création d\'une version',
        ],
        'store' => [
            'success' => 'Création d\'une nouvelle version : :uuid',
            'error' => 'Erreur lors de la création d\'une version',
            'flash_success' => 'Version (:name) créée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'show' => [
            'no_name' => 'Aucun nom trouvé, pas de traduction disponible',
            'success' => 'Consultation du détail de la version : :name',
            'warning' => 'Tentative de consultation d\'une version introuvable : :uuid',
            'error' => 'Erreur lors de la consultation de la version : :uuid',
            'slug_mismatch' => 'Incohérence du slug pour la version',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification de la version : :name',
            'warning' => 'Tentative de modification d\'une version introuvable : :uuid',
            'error' => 'Erreur lors de l\'accès à la page de modification de la version : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour de la version en base de données : :name',
            'warning' => 'Tentative de mise à jour d\'une version introuvable : :uuid',
            'warning_unpub' => 'Erreur métier lors de la dépublication de la version : :uuid',
            'error' => 'Erreur lors de la mise à jour de la version : :uuid',
            'flash_success' => 'Version (:name) mise à jour avec succès.',
            'flash_warning' => 'Version introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Suppression de la version en base de données : :name',
            'warning' => 'Tentative de suppression d\'une version introuvable : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression de la version : :uuid',
            'error' => 'Erreur lors de la suppression de la version : :uuid',
            'flash_success' => 'Version (:name) supprimée avec succès.',
            'flash_warning' => 'Version introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'activate' => [
            'success' => 'Activation de la version : :name',
            'error' => 'Erreur lors de l\'activation de la version : :uuid',
            'flash_success' => 'Version (:name) activée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning' => 'Tentative d\'activation d\'une version introuvable : :uuid',
            'flash_warning' => 'Version introuvable.',
            'warning_meta' => 'Erreur métier lors de l\'activation de la version : :uuid',
        ],
        'deactivate' => [
            'success' => 'Désactivation de la version : :name',
            'error' => 'Erreur lors de la désactivation de la version : :uuid',
            'flash_success' => 'Version (:name) désactivée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning' => 'Tentative de désactivation d\'une version introuvable : :uuid',
            'flash_warning' => 'Version introuvable.',
            'warning_meta' => 'Erreur métier lors de la désactivation de la version : :uuid',
        ],
        'audits' => [
            'slug_mismatch' => 'Incohérence du slug pour la version',
            'success' => 'Consultation des audits de la version :name',
            'error' => 'Erreur lors de la consultation des audits de la version : :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Incohérence du slug pour la version',
            'success' => 'Consultation des journaux d\'activité de la version :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité de la version : :uuid',
        ],
        'publish' => [
            'success' => 'Publication de la version : :name',
            'error' => 'Erreur lors de la publication de la version : :uuid',
            'flash_success' => 'Version (:name) publiée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning_meta' => 'Erreur métier lors de la publication de la version : :uuid',
        ],
        'unpublish' => [
            'success' => 'Dépublication de la version : :code',
            'warning' => 'Erreur métier lors de la dépublication de la version : :uuid',
            'error' => 'Erreur lors de la dépublication de la version : :uuid',
            'flash_success' => 'Version (:name) dépubliée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Consultation de la corbeille des versions',
            'error' => 'Erreur lors de la consultation de la corbeille des versions',
        ],
        'restore' => [
            'success' => 'Restauration de la version : :name',
            'warning' => 'Tentative de restauration d\'une version introuvable : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration de la version : :uuid',
            'error' => 'Erreur lors de la restauration de la version : :uuid',
            'flash_success' => 'Version (:name) restaurée avec succès.',
            'flash_warning' => 'Version introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive de la version : :name',
            'warning' => 'Tentative de suppression définitive d\'une version introuvable : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive de la version : :uuid',
            'error' => 'Erreur lors de la suppression définitive de la version : :uuid',
            'flash_success' => 'Version (:name) supprimée définitivement avec succès.',
            'flash_warning' => 'Version introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'unknown' => 'Version inconnue',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        'translations' => [
            'not_found' => 'Tentative d\'accès à une traduction introuvable (UUID: :uuid).',
            'destroy' => [
                'success' => 'Suppression de la traduction : :uuid',
                'warning' => 'Tentative de suppression d\'une traduction introuvable : :uuid',
                'warning_meta' => 'Erreur métier lors de la suppression de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'trashed' => [
                'success' => 'Consultation de la corbeille des traductions de la version : :code',
                'error' => 'Erreur lors de la consultation de la corbeille des traductions',
            ],
            'restore' => [
                'success' => 'Restauration de la traduction : :uuid',
                'warning' => 'Tentative de restauration d\'une traduction introuvable : :uuid',
                'warning_meta' => 'Erreur métier lors de la restauration de la traduction : :uuid',
                'error' => 'Erreur lors de la restauration de la traduction : :uuid',
                'flash_success' => 'Traduction restaurée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'force_destroy' => [
                'success' => 'Suppression définitive de la traduction : :uuid',
                'warning' => 'Tentative de suppression définitive d\'une traduction introuvable : :uuid',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression définitive de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],

        ],

    ],
];
