<?php

return [
    'productWarehouseStocks' => [
        'not_found' => 'Tentative d\'accès à un stock introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des stocks',
            'error' => 'Erreur lors de l\'affichage de la liste des stocks',
        ],
        'create' => [
            'success' => 'Accès à la page de création d\'un stock',
            'error' => 'Erreur lors de l\'accès à la page de création d\'un stock',
        ],
        'store' => [
            'success' => 'Création d\'un nouveau stock',
            'error' => 'Erreur lors de la création du stock',
            'flash_success' => 'Stock créé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'show' => [
            'success' => 'Consultation des détails du stock',
            'warning' => 'Tentative de consultation d\'un stock inexistant : :uuid',
            'error' => 'Erreur lors de la consultation du stock : :uuid',
        ],
        'audits' => [
            'success' => 'Consultation des audits du stock',
            'warning' => 'Tentative de consultation des audits d\'un stock inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des audits du stock : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité du stock',
            'warning' => 'Tentative de consultation des journaux d\'activité d\'un stock inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des journaux d\'activité du stock : :uuid',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification du stock',
            'warning' => 'Tentative de modification d\'un stock inexistant : :uuid',
            'error' => 'Erreur lors de l\'accès à la page de modification du stock : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour du stock en base de données',
            'warning' => 'Tentative de mise à jour d\'un stock inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la mise à jour du stock : :uuid',
            'error' => 'Erreur lors de la mise à jour du stock : :uuid',
            'flash_success' => 'Stock mis à jour avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Suppression du stock',
            'warning' => 'Tentative de suppression d\'un stock inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression du stock : :uuid',
            'error' => 'Erreur lors de la suppression du stock : :uuid',
            'flash_success' => 'Stock supprimé avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'unknown' => 'Stock inconnu',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des stocks supprimés',
            'error' => 'Erreur lors de l\'affichage de la liste des stocks supprimés',
        ],
        'restore' => [
            'success' => 'Restauration du stock',
            'warning' => 'Tentative de restauration d\'un stock inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration du stock : :uuid',
            'error' => 'Erreur lors de la restauration du stock : :uuid',
            'flash_success' => 'Stock restauré avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive du stock',
            'warning' => 'Tentative de suppression définitive d\'un stock inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive du stock : :uuid',
            'error' => 'Erreur lors de la suppression définitive du stock : :uuid',
            'flash_success' => 'Stock supprimé définitivement avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
    ],
];
