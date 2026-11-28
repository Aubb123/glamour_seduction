<?php

return [
    'warehouseStocks' => [
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
            'success' => 'Création d\'un nouveau stock : :label',
            'error' => 'Erreur lors de la création d\'un stock',
            'flash_success' => 'Stock (:label) créé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'show' => [
            'success' => 'Consultation des détails du stock : :label',
            'error' => 'Erreur lors de la consultation du stock : :uuid',
        ],
        'audits' => [
            'success' => 'Consultation des audits du stock : :label',
            'error' => 'Erreur lors de la consultation des audits du stock : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des logs d\'activité du stock : :label',
            'error' => 'Erreur lors de la consultation des logs d\'activité du stock : :uuid',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification du stock : :label',
            'error' => 'Erreur lors de l\'accès à la page de modification du stock : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour du stock en base de données : :label',
            'warning_meta' => 'Erreur métier lors de la mise à jour du stock : :uuid',
            'error' => 'Erreur lors de la mise à jour du stock : :uuid',
            'flash_success' => 'Stock (:label) mis à jour avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Suppression du stock : :label',
            'warning_meta' => 'Erreur métier lors de la suppression du stock : :uuid',
            'error' => 'Erreur lors de la suppression du stock : :uuid',
            'flash_success' => 'Stock (:label) supprimé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage des stocks en corbeille',
            'error' => 'Erreur lors de l\'affichage des stocks en corbeille',
        ],
        'restore' => [
            'success' => 'Restauration du stock : :label',
            'flash_success' => 'Stock (:label) restauré avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'error' => 'Erreur lors de la restauration du stock : :uuid',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive du stock : :label',
            'flash_success' => 'Stock (:label) supprimé définitivement avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'error' => 'Erreur lors de la suppression définitive du stock : :uuid',
        ],
    ],
];
