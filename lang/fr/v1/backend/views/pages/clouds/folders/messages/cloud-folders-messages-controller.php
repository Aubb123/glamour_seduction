<?php

return [
    'cloud-folders' => [
        'not_found' => 'Tentative d\'accès à un dossier cloud introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des dossiers cloud',
            'error' => 'Erreur lors de l\'affichage de la liste des dossiers cloud',
        ],
        'show' => [
            'no_name' => 'Aucun nom trouvé',
            'success' => 'Consultation des détails du dossier cloud : :name',
            'warning' => 'Tentative de consultation d\'un dossier cloud inexistant : :uuid',
            'error' => 'Erreur lors de la consultation du dossier cloud : :uuid',
        ],
        'files' => [
            'success' => 'Consultation des fichiers du dossier cloud : :name',
            'warning' => 'Tentative de consultation des fichiers d\'un dossier cloud inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des fichiers du dossier cloud : :uuid',
        ],
        'subfolders' => [
            'success' => 'Consultation des sous-dossiers du dossier cloud : :name',
            'warning' => 'Tentative de consultation des sous-dossiers d\'un dossier cloud inexistant : :uuid',
            'error' => 'Erreur lors de la consultation des sous-dossiers du dossier cloud : :uuid',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification du dossier cloud : :name',
            'warning' => 'Tentative de modification d\'un dossier cloud inexistant : :uuid',
            'error' => 'Erreur lors de l\'accès à la page de modification du dossier cloud : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour du dossier cloud : :name',
            'warning' => 'Tentative de mise à jour d\'un dossier cloud inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la mise à jour du dossier cloud : :uuid',
            'error' => 'Erreur lors de la mise à jour du dossier cloud : :uuid',
            'flash_success' => 'Dossier cloud (:name) mis à jour avec succès.',
            'flash_warning' => 'Dossier cloud introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la mise à jour.',
        ],
        'toggle_active' => [
            'success_activated' => 'Dossier cloud activé : :name',
            'success_deactivated' => 'Dossier cloud désactivé : :name',
            'error' => 'Erreur lors du changement de statut du dossier cloud : :uuid',
            'flash_success_activated' => 'Dossier cloud (:name) activé avec succès.',
            'flash_success_deactivated' => 'Dossier cloud (:name) désactivé avec succès.',
            'flash_error' => 'Une erreur est survenue lors du changement de statut.',
            'warning' => 'Tentative de changement de statut d\'un dossier cloud inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors du changement de statut du dossier cloud : :uuid',
            'flash_warning' => 'Dossier cloud introuvable.',
        ],
    ],
];
