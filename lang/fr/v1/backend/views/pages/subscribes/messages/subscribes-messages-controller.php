<?php

return [
    'subscribes' => [
        'not_found' => 'Tentative d\'accès à un abonné introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des abonnés',
            'error' => 'Erreur lors de l\'affichage de la liste des abonnés',
        ],
        'show' => [
            'success' => 'Consultation des détails de l\'abonné : :name',
            'warning' => 'Tentative de consultation d\'un abonné inexistant : :uuid',
            'error' => 'Erreur lors de la consultation de l\'abonné : :uuid',
        ],
        'activate' => [
            'success' => 'Abonné activé : :name',
            'warning' => 'Tentative d\'activation d\'un abonné inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de l\'activation de l\'abonné : :uuid',
            'error' => 'Erreur lors de l\'activation de l\'abonné : :uuid',
            'flash_success' => 'Abonné activé avec succès.',
            'flash_warning' => 'Abonné introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'deactivate' => [
            'success' => 'Abonné désactivé : :name',
            'warning' => 'Tentative de désactivation d\'un abonné inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la désactivation de l\'abonné : :uuid',
            'error' => 'Erreur lors de la désactivation de l\'abonné : :uuid',
            'flash_success' => 'Abonné désactivé avec succès.',
            'flash_warning' => 'Abonné introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Suppression de l\'abonné en base de données : :name',
            'warning' => 'Tentative de suppression d\'un abonné inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression de l\'abonné : :uuid',
            'error' => 'Erreur lors de la suppression de l\'abonné : :uuid',
            'flash_success' => 'Abonné (:name) supprimé avec succès.',
            'flash_warning' => 'Abonné introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des abonnés supprimés',
            'error' => 'Erreur lors de l\'affichage de la liste des abonnés supprimés',
        ],
        'restore' => [
            'success' => 'Restauration de l\'abonné : :name',
            'warning' => 'Tentative de restauration d\'un abonné inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration de l\'abonné : :uuid',
            'error' => 'Erreur lors de la restauration de l\'abonné : :uuid',
            'flash_success' => 'Abonné (:name) restauré avec succès.',
            'flash_warning' => 'Abonné introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive de l\'abonné : :name',
            'warning' => 'Tentative de suppression définitive d\'un abonné inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive de l\'abonné : :uuid',
            'error' => 'Erreur lors de la suppression définitive de l\'abonné : :uuid',
            'flash_success' => 'Abonné supprimé définitivement avec succès.',
            'flash_warning' => 'Abonné introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],

        // ────────────────────────────────────────────
        // HISTORIQUE DES ABONNEMENTS
        // ────────────────────────────────────────────
        'histories' => [
            'not_found' => 'Tentative d\'accès à un historique d\'abonné introuvable (UUID: :uuid).',
            'index' => [
                'success' => 'Affichage de la liste des historiques d\'abonnement',
                'error' => 'Erreur lors de l\'affichage de la liste des historiques',
            ],
            'show' => [
                'success' => 'Consultation des détails de l\'historique : :uuid',
                'warning' => 'Tentative de consultation d\'un historique inexistant : :uuid',
                'error' => 'Erreur lors de la consultation de l\'historique : :uuid',
            ],
            'destroy' => [
                'success' => 'Suppression de l\'historique : :uuid',
                'warning' => 'Tentative de suppression d\'un historique inexistant : :uuid',
                'warning_meta' => 'Erreur métier lors de la suppression de l\'historique : :uuid',
                'error' => 'Erreur lors de la suppression de l\'historique : :uuid',
                'flash_success' => 'Historique supprimé avec succès.',
                'flash_warning' => 'Historique introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'trashed' => [
                'success' => 'Affichage de la liste des historiques supprimés',
                'error' => 'Erreur lors de l\'affichage de la liste des historiques supprimés',
            ],
            'restore' => [
                'success' => 'Restauration de l\'historique : :uuid',
                'warning' => 'Tentative de restauration d\'un historique inexistant : :uuid',
                'warning_meta' => 'Erreur métier lors de la restauration de l\'historique : :uuid',
                'error' => 'Erreur lors de la restauration de l\'historique : :uuid',
                'flash_success' => 'Historique restauré avec succès.',
                'flash_warning' => 'Historique introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'force_destroy' => [
                'success' => 'Suppression définitive de l\'historique : :uuid',
                'warning' => 'Tentative de suppression définitive d\'un historique inexistant : :uuid',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de l\'historique : :uuid',
                'error' => 'Erreur lors de la suppression définitive de l\'historique : :uuid',
                'flash_success' => 'Historique supprimé définitivement avec succès.',
                'flash_warning' => 'Historique introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
        ],
    ],
];
