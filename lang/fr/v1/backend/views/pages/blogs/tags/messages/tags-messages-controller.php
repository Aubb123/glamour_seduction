<?php

return [
    'tags' => [
        'not_found' => 'Tentative d\'accès à un tag de blog introuvable (UUID: :uuid).',
        // ───────────────────────────────────────
        // INDEX
        // ───────────────────────────────────────
        'index' => [
            'success' => 'Affichage de la liste des tags de blog',
            'error' => 'Erreur lors de l\'affichage de la liste des tags de blog',
        ],

        // ───────────────────────────────────────
        // CREATE
        // ───────────────────────────────────────
        'create' => [
            'success' => 'Accès à la page de création d\'un tag de blog',
            'error' => 'Erreur lors de l\'accès à la page de création d\'un tag de blog',
        ],

        // ───────────────────────────────────────
        // STORE
        // ───────────────────────────────────────
        'store' => [
            'success' => 'Création d\'un nouveau tag de blog : :name',
            'error' => 'Erreur lors de la création d\'un tag de blog',
            'flash_success' => 'Tag (:name) créé avec succès.',
            'flash_error' => 'Une erreur est survenue lors de la création du tag.',
        ],

        // ───────────────────────────────────────
        // SHOW
        // ───────────────────────────────────────
        'show' => [
            'no_name' => 'Aucun nom trouvé',
            'success' => 'Consultation des détails du tag : :name',
            'warning' => 'Tentative de consultation d\'un tag inexistant : :uuid',
            'error' => 'Erreur lors de la consultation du tag : :uuid',
            'slug_mismatch' => 'Le slug du tag de blog ne correspond pas au slug attendu.',
        ],

        // ───────────────────────────────────────
        // POSTS
        // ───────────────────────────────────────
        'posts' => [
            'no_name' => 'Aucun nom trouvé',
            'success' => 'Consultation des articles du tag : :name',
            'warning' => 'Tentative de consultation des articles d\'un tag inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des articles du tag : :uuid',
            'slug_mismatch' => 'Le slug du tag de blog ne correspond pas au slug attendu.',
        ],

        // ───────────────────────────────────────
        // AUDITS
        // ───────────────────────────────────────
        'audits' => [
            'success' => 'Consultation des audits du tag : :name',
            'warning' => 'Tentative de consultation des audits d\'un tag inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des audits du tag : :uuid',
            'slug_mismatch' => 'Le slug du tag de blog ne correspond pas au slug attendu.',
        ],

        // ───────────────────────────────────────
        // ACTIVITY LOGS
        // ───────────────────────────────────────
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité du tag : :name',
            'warning' => 'Tentative de consultation des journaux d\'activité d\'un tag inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des journaux d\'activité du tag : :uuid',
            'slug_mismatch' => 'Le slug du tag de blog ne correspond pas au slug attendu.',
        ],

        // ───────────────────────────────────────
        // EDIT
        // ───────────────────────────────────────
        'edit' => [
            'success' => 'Accès à la page de modification du tag : :name',
            'warning' => 'Tentative de modification d\'un tag inexistant : :uuid',
            'error' => 'Erreur lors de l\'accès à la page de modification du tag : :uuid',
        ],

        // ───────────────────────────────────────
        // UPDATE
        // ───────────────────────────────────────
        'update' => [
            'success' => 'Mise à jour du tag en base de données : :name',
            'warning' => 'Tentative de mise à jour d\'un tag inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la mise à jour du tag : :uuid',
            'error' => 'Erreur lors de la mise à jour du tag : :uuid',
            'flash_success' => 'Tag (:name) mis à jour avec succès.',
            'flash_warning' => 'Tag introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la mise à jour du tag.',
        ],

        // ───────────────────────────────────────
        // ACTIVATE
        // ───────────────────────────────────────
        'activate' => [
            'success' => 'Activation du tag : :name',
            'error' => 'Erreur lors de l\'activation du tag : :uuid',
            'flash_success' => 'Tag (:name) activé avec succès.',
            'flash_error' => 'Une erreur est survenue lors de l\'activation du tag.',
            'warning' => 'Tentative d\'activation d\'un tag inexistant : :uuid',
            'flash_warning' => 'Tag introuvable.',
            'warning_meta' => 'Erreur métier lors de l\'activation du tag : :uuid',
        ],

        // ───────────────────────────────────────
        // DEACTIVATE
        // ───────────────────────────────────────
        'deactivate' => [
            'success' => 'Désactivation du tag : :name',
            'error' => 'Erreur lors de la désactivation du tag : :uuid',
            'flash_success' => 'Tag (:name) désactivé avec succès.',
            'flash_error' => 'Une erreur est survenue lors de la désactivation du tag.',
            'warning' => 'Tentative de désactivation d\'un tag inexistant : :uuid',
            'flash_warning' => 'Tag introuvable.',
            'warning_meta' => 'Erreur métier lors de la désactivation du tag : :uuid',
        ],

        // ───────────────────────────────────────
        // DESTROY
        // ───────────────────────────────────────
        'destroy' => [
            'success' => 'Suppression du tag en base de données : :name',
            'warning' => 'Tentative de suppression d\'un tag inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression du tag : :uuid',
            'error' => 'Erreur lors de la suppression du tag : :uuid',
            'flash_success' => 'Tag (:name) supprimé avec succès.',
            'flash_warning' => 'Tag introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression du tag.',
            'unknown' => 'Tag inconnu',
        ],

        // ───────────────────────────────────────
        // TRASHED
        // ───────────────────────────────────────
        'trashed' => [
            'success' => 'Affichage de la liste des tags supprimés',
            'error' => 'Erreur lors de l\'affichage de la liste des tags supprimés',
        ],

        // ───────────────────────────────────────
        // RESTORE
        // ───────────────────────────────────────
        'restore' => [
            'success' => 'Restauration du tag : :name',
            'warning' => 'Tentative de restauration d\'un tag inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration du tag : :uuid',
            'error' => 'Erreur lors de la restauration du tag : :uuid',
            'flash_success' => 'Tag (:name) restauré avec succès.',
            'flash_warning' => 'Tag introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la restauration du tag.',
        ],

        // ───────────────────────────────────────
        // FORCE DESTROY
        // ───────────────────────────────────────
        'force_destroy' => [
            'success' => 'Suppression définitive du tag : :name',
            'warning' => 'Tentative de suppression définitive d\'un tag inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive du tag : :uuid',
            'error' => 'Erreur lors de la suppression définitive du tag : :uuid',
            'flash_success' => 'Tag supprimé définitivement avec succès.',
            'flash_warning' => 'Tag introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la suppression définitive du tag.',
            'unknown' => 'Tag inconnu',
        ],

        // ── Traductions ──
        'translations' => [
            'not_found' => 'Tentative d\'accès à une traduction de tag de blog introuvable (UUID: :uuid).',
            'destroy' => [
                'success' => 'Suppression de la traduction : :uuid',
                'warning' => 'Tentative de suppression d\'une traduction inexistante : :uuid',
                'warning_meta' => 'Erreur de validation lors de la suppression de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la suppression de la traduction.',
                'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction du tag.',
            ],
            'trashed' => [
                'accessed' => 'Affichage des traductions supprimées pour le tag : :name',
                'not_found' => 'Tag introuvable pour l\'affichage des traductions supprimées : :uuid',
                'error' => 'Erreur lors de l\'affichage des traductions supprimées : :uuid',
            ],
            'restore' => [
                'success' => 'Restauration de la traduction : :uuid',
                'warning' => 'Tentative de restauration d\'une traduction inexistante : :uuid',
                'warning_meta' => 'Erreur de validation lors de la restauration de la traduction : :uuid',
                'error' => 'Erreur lors de la restauration de la traduction : :uuid',
                'flash_success' => 'Traduction restaurée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue lors de la restauration de la traduction.',
                'last_translation_forbidden' => 'Impossible de restaurer la dernière traduction du tag.',
            ],
            'force_destroy' => [
                'success' => 'Suppression définitive de la traduction : :uuid',
                'warning' => 'Tentative de suppression définitive d\'une traduction inexistante : :uuid',
                'warning_meta' => 'Erreur de validation lors de la suppression définitive de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression définitive de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction introuvable pour suppression définitive.',
                'flash_error' => 'Une erreur est survenue lors de la suppression définitive de la traduction.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction du tag.',
            ],
        ],
    ],
];
