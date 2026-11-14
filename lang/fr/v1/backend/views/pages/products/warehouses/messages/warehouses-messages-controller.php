<?php

return [
    'productWarehouses' => [
        'not_found' => 'Tentative d\'accès à un entrepôt introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des entrepôts',
            'error' => 'Erreur lors de l\'affichage de la liste des entrepôts',
        ],
        'create' => [
            'success' => 'Accès à la page de création d\'un entrepôt',
            'error' => 'Erreur lors de l\'accès à la page de création d\'un entrepôt',
        ],
        'store' => [
            'success' => 'Création d\'un nouvel entrepôt : :name',
            'error' => 'Erreur lors de la création de l\'entrepôt',
            'flash_success' => 'Entrepôt (:name) créé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'show' => [
            'success' => 'Consultation des détails de l\'entrepôt : :name',
            'warning' => 'Tentative de consultation d\'un entrepôt inexistant : :uuid',
            'error' => 'Erreur lors de la consultation de l\'entrepôt : :uuid',
        ],
        'audits' => [
            'success' => 'Consultation des audits de l\'entrepôt : :name',
            'warning' => 'Tentative de consultation des audits d\'un entrepôt inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des audits de l\'entrepôt : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité de l\'entrepôt : :name',
            'warning' => 'Tentative de consultation des journaux d\'activité d\'un entrepôt inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des journaux d\'activité de l\'entrepôt : :uuid',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification de l\'entrepôt : :name',
            'warning' => 'Tentative de modification d\'un entrepôt inexistant : :uuid',
            'error' => 'Erreur lors de l\'accès à la page de modification de l\'entrepôt : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour de l\'entrepôt en base de données : :name',
            'warning' => 'Tentative de mise à jour d\'un entrepôt inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la mise à jour de l\'entrepôt : :uuid',
            'error' => 'Erreur lors de la mise à jour de l\'entrepôt : :uuid',
            'flash_success' => 'Entrepôt (:name) mis à jour avec succès.',
            'flash_warning' => 'Entrepôt introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'activate' => [
            'success' => 'Activation de l\'entrepôt : :name',
            'error' => 'Erreur lors de l\'activation de l\'entrepôt : :uuid',
            'flash_success' => 'Entrepôt (:name) activé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning' => 'Tentative d\'activation d\'un entrepôt inexistant : :uuid',
            'flash_warning' => 'Entrepôt introuvable.',
            'warning_meta' => 'Erreur métier lors de l\'activation de l\'entrepôt : :uuid',
        ],
        'deactivate' => [
            'success' => 'Désactivation de l\'entrepôt : :name',
            'error' => 'Erreur lors de la désactivation de l\'entrepôt : :uuid',
            'flash_success' => 'Entrepôt (:name) désactivé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning' => 'Tentative de désactivation d\'un entrepôt inexistant : :uuid',
            'flash_warning' => 'Entrepôt introuvable.',
            'warning_meta' => 'Erreur métier lors de la désactivation de l\'entrepôt : :uuid',
        ],
        'destroy' => [
            'success' => 'Suppression de l\'entrepôt : :name',
            'warning' => 'Tentative de suppression d\'un entrepôt inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression de l\'entrepôt : :uuid',
            'error' => 'Erreur lors de la suppression de l\'entrepôt : :uuid',
            'flash_success' => 'Entrepôt (:name) supprimé avec succès.',
            'flash_warning' => 'Entrepôt introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'unknown' => 'Entrepôt inconnu',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des entrepôts supprimés',
            'error' => 'Erreur lors de l\'affichage de la liste des entrepôts supprimés',
        ],
        'restore' => [
            'success' => 'Restauration de l\'entrepôt : :name',
            'warning' => 'Tentative de restauration d\'un entrepôt inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration de l\'entrepôt : :uuid',
            'error' => 'Erreur lors de la restauration de l\'entrepôt : :uuid',
            'flash_success' => 'Entrepôt (:name) restauré avec succès.',
            'flash_warning' => 'Entrepôt introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive de l\'entrepôt : :name',
            'warning' => 'Tentative de suppression définitive d\'un entrepôt inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive de l\'entrepôt : :uuid',
            'error' => 'Erreur lors de la suppression définitive de l\'entrepôt : :uuid',
            'flash_success' => 'Entrepôt supprimé définitivement avec succès.',
            'flash_warning' => 'Entrepôt introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],

        // Traductions (product_warehouse_translations)
        'translations' => [
            'not_found' => 'Tentative d\'accès à une traduction d\'entrepôt introuvable (UUID: :uuid).',
            'destroy' => [
                'success' => 'Suppression de la traduction : :uuid',
                'warning' => 'Tentative de suppression d\'une traduction inexistante : :uuid',
                'warning_meta' => 'Erreur de validation lors de la suppression de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'trashed' => [
                'accessed' => 'Affichage des traductions supprimées pour l\'entrepôt : :name',
                'not_found' => 'Entrepôt introuvable pour l\'affichage des traductions supprimées : :uuid',
                'error' => 'Erreur lors de l\'affichage des traductions supprimées : :uuid',
            ],
            'restore' => [
                'success' => 'Restauration de la traduction : :uuid',
                'warning' => 'Tentative de restauration d\'une traduction inexistante : :uuid',
                'warning_meta' => 'Erreur de validation lors de la restauration de la traduction : :uuid',
                'error' => 'Erreur lors de la restauration de la traduction : :uuid',
                'flash_success' => 'Traduction restaurée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'force_destroy' => [
                'success' => 'Suppression définitive de la traduction : :uuid',
                'warning' => 'Tentative de suppression définitive d\'une traduction inexistante : :uuid',
                'warning_meta' => 'Erreur de validation lors de la suppression définitive de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression définitive de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
        ],
    ],

    // Personnel des entrepôts (pivot `warehouse_users`) — messages d'activité (WarehouseUserController)
    'warehouseUsers' => [
        'store' => [
            'success' => 'Affectation d\'un membre du personnel à l\'entrepôt : :name',
            'error' => 'Erreur lors de l\'affectation du membre du personnel',
            'not_found' => 'Utilisateur introuvable.',
            'duplicate' => 'Cet utilisateur est déjà affecté à cet entrepôt.',
            'flash_success' => 'Membre du personnel (:name) affecté avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'update' => [
            'success' => 'Mise à jour de l\'affectation : :name',
            'warning_meta' => 'Erreur métier lors de la mise à jour de l\'affectation : :uuid',
            'error' => 'Erreur lors de la mise à jour de l\'affectation : :uuid',
            'not_found' => 'Affectation introuvable.',
            'flash_success' => 'Affectation (:name) mise à jour avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Retrait d\'un membre du personnel : :name',
            'error' => 'Erreur lors du retrait du membre du personnel : :uuid',
            'not_found' => 'Affectation introuvable.',
            'flash_success' => 'Membre du personnel (:name) retiré avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
    ],
];
