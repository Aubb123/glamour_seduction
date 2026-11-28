<?php

return [
    'warehouseLocationStocks' => [
        'not_found' => 'Tentative d\'accès à un stock par emplacement introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des stocks par emplacement',
            'error' => 'Erreur lors de l\'affichage de la liste des stocks par emplacement',
        ],
        'create' => [
            'success' => 'Accès à la page de création d\'un stock par emplacement',
            'error' => 'Erreur lors de l\'accès à la page de création d\'un stock par emplacement',
        ],
        'store' => [
            'success' => 'Création d\'un nouveau stock par emplacement : :label',
            'error' => 'Erreur lors de la création d\'un stock par emplacement',
            'flash_success' => 'Stock par emplacement (:label) créé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'show' => [
            'success' => 'Consultation des détails du stock par emplacement : :label',
            'error' => 'Erreur lors de la consultation du stock par emplacement : :uuid',
        ],
        'audits' => [
            'success' => 'Consultation des audits du stock par emplacement : :label',
            'error' => 'Erreur lors de la consultation des audits du stock par emplacement : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des logs d\'activité du stock par emplacement : :label',
            'error' => 'Erreur lors de la consultation des logs d\'activité du stock par emplacement : :uuid',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification du stock par emplacement : :label',
            'error' => 'Erreur lors de l\'accès à la page de modification du stock par emplacement : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour du stock par emplacement en base de données : :label',
            'warning_meta' => 'Erreur métier lors de la mise à jour du stock par emplacement : :uuid',
            'error' => 'Erreur lors de la mise à jour du stock par emplacement : :uuid',
            'flash_success' => 'Stock par emplacement (:label) mis à jour avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Suppression du stock par emplacement : :label',
            'warning_meta' => 'Erreur métier lors de la suppression du stock par emplacement : :uuid',
            'error' => 'Erreur lors de la suppression du stock par emplacement : :uuid',
            'flash_success' => 'Stock par emplacement (:label) supprimé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage des stocks par emplacement en corbeille',
            'error' => 'Erreur lors de l\'affichage des stocks par emplacement en corbeille',
        ],
        'restore' => [
            'success' => 'Restauration du stock par emplacement : :label',
            'flash_success' => 'Stock par emplacement (:label) restauré avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'error' => 'Erreur lors de la restauration du stock par emplacement : :uuid',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive du stock par emplacement : :label',
            'flash_success' => 'Stock par emplacement (:label) supprimé définitivement avec succès.',
            'flash_warning' => 'Stock introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'error' => 'Erreur lors de la suppression définitive du stock par emplacement : :uuid',
        ],
    ],
];
