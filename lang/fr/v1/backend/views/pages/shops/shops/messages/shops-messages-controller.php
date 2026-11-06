<?php

return [
    'shops' => [
        'not_found' => 'Tentative d\'accès à une boutique introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des boutiques',
            'error' => 'Erreur lors de l\'affichage de la liste des boutiques',
        ],
        'create' => [
            'success' => 'Accès à la page de création d\'une boutique',
            'error' => 'Erreur lors de l\'accès à la page de création d\'une boutique',
        ],
        'store' => [
            'success' => 'Création d\'une nouvelle boutique : :name',
            'error' => 'Erreur lors de la création de la boutique',
            'flash_success' => 'Boutique (:name) créée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'show' => [
            'success' => 'Consultation des détails de la boutique : :name',
            'warning' => 'Tentative de consultation d\'une boutique inexistante : :uuid',
            'error' => 'Erreur lors de la consultation de la boutique : :uuid',
        ],
        'audits' => [
            'success' => 'Consultation des audits de la boutique : :name',
            'error' => 'Erreur lors de la consultation des audits de la boutique : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité de la boutique : :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité de la boutique : :uuid',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification de la boutique : :name',
            'error' => 'Erreur lors de l\'accès à la page de modification de la boutique : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour de la boutique : :name',
            'warning_meta' => 'Erreur métier lors de la mise à jour de la boutique : :uuid',
            'error' => 'Erreur lors de la mise à jour de la boutique : :uuid',
            'flash_success' => 'Boutique (:name) mise à jour avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'activate' => [
            'success' => 'Activation de la boutique : :name',
            'warning_meta' => 'Erreur métier lors de l\'activation de la boutique : :uuid',
            'error' => 'Erreur lors de l\'activation de la boutique : :uuid',
            'flash_success' => 'Boutique (:name) activée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'deactivate' => [
            'success' => 'Désactivation de la boutique : :name',
            'warning_meta' => 'Erreur métier lors de la désactivation de la boutique : :uuid',
            'error' => 'Erreur lors de la désactivation de la boutique : :uuid',
            'flash_success' => 'Boutique (:name) désactivée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Suppression de la boutique : :name',
            'warning_meta' => 'Erreur métier lors de la suppression de la boutique : :uuid',
            'error' => 'Erreur lors de la suppression de la boutique : :uuid',
            'flash_success' => 'Boutique (:name) supprimée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des boutiques supprimées',
            'error' => 'Erreur lors de l\'affichage de la liste des boutiques supprimées',
        ],
        'restore' => [
            'success' => 'Restauration de la boutique : :name',
            'warning' => 'Tentative de restauration d\'une boutique inexistante : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration de la boutique : :uuid',
            'error' => 'Erreur lors de la restauration de la boutique : :uuid',
            'flash_success' => 'Boutique (:name) restaurée avec succès.',
            'flash_warning' => 'Boutique introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive de la boutique : :name',
            'warning' => 'Tentative de suppression définitive d\'une boutique inexistante : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive de la boutique : :uuid',
            'error' => 'Erreur lors de la suppression définitive de la boutique : :uuid',
            'flash_success' => 'Boutique supprimée définitivement avec succès.',
            'flash_warning' => 'Boutique introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'translations' => [
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
                'accessed' => 'Affichage de la corbeille des traductions de la boutique : :name',
                'not_found' => 'Boutique introuvable (UUID: :uuid).',
                'error' => 'Erreur lors de l\'affichage de la corbeille des traductions : :uuid',
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
