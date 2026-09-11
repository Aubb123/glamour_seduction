<?php

return [
    'productStockMovements' => [
        'not_found' => 'Tentative d\'accès à un mouvement de stock introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des mouvements de stock',
            'error' => 'Erreur lors de l\'affichage de la liste des mouvements de stock',
        ],
        'create' => [
            'success' => 'Accès à la page de création d\'un mouvement de stock',
            'error' => 'Erreur lors de l\'accès à la page de création d\'un mouvement de stock',
        ],
        'store' => [
            'success' => 'Enregistrement d\'un nouveau mouvement de stock',
            'error' => 'Erreur lors de l\'enregistrement du mouvement de stock',
            'flash_success' => 'Mouvement de stock enregistré avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'show' => [
            'success' => 'Consultation des détails du mouvement de stock',
            'warning' => 'Tentative de consultation d\'un mouvement inexistant : :uuid',
            'error' => 'Erreur lors de la consultation du mouvement de stock : :uuid',
        ],
        'destroy' => [
            'success' => 'Suppression du mouvement de stock',
            'warning' => 'Tentative de suppression d\'un mouvement inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression du mouvement : :uuid',
            'error' => 'Erreur lors de la suppression du mouvement : :uuid',
            'flash_success' => 'Mouvement supprimé avec succès.',
            'flash_warning' => 'Mouvement introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des mouvements de stock supprimés',
            'error' => 'Erreur lors de l\'affichage de la liste des mouvements supprimés',
        ],
        'restore' => [
            'success' => 'Restauration du mouvement de stock',
            'warning' => 'Tentative de restauration d\'un mouvement inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration du mouvement : :uuid',
            'error' => 'Erreur lors de la restauration du mouvement : :uuid',
            'flash_success' => 'Mouvement de stock restauré avec succès.',
            'flash_warning' => 'Mouvement introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive du mouvement de stock',
            'warning' => 'Tentative de suppression définitive d\'un mouvement inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive du mouvement : :uuid',
            'error' => 'Erreur lors de la suppression définitive du mouvement : :uuid',
            'flash_success' => 'Mouvement supprimé définitivement avec succès.',
            'flash_warning' => 'Mouvement introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
    ],
];
