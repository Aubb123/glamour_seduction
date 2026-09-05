<?php

return [
    'cloud-files' => [
        'not_found' => 'Tentative d\'accès à un fichier cloud introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Consultation de la liste des fichiers cloud',
            'error' => 'Erreur lors de la consultation de la liste des fichiers cloud',
        ],
        'show' => [
            'success' => 'Consultation du détail du fichier cloud : :name',
            'warning' => 'Tentative de consultation d\'un fichier cloud introuvable : :uuid',
            'error' => 'Erreur lors de la consultation du fichier cloud : :uuid',
            'not_found' => 'Le fichier cloud avec l\'UUID :uuid est introuvable.',
            'proxy_error' => 'Erreur du proxy de fichier cloud : :message',
        ],
        'audits' => [
            'success' => 'Consultation des audits du fichier cloud : :name',
            'error' => 'Erreur lors de la consultation des audits du fichier cloud : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité du fichier cloud : :name',
            'error' => 'Erreur lors de la consultation des journaux d\'activité du fichier cloud : :uuid',
        ],
        'destroy' => [
            'success' => 'Suppression du fichier cloud : :name',
            'flash_success' => 'Fichier cloud (:name) supprimé avec succès.',
            'warning' => 'Tentative de suppression d\'un fichier cloud introuvable : :uuid',
            'flash_warning' => 'Fichier cloud introuvable.',
            'invalid_argument' => 'Argument invalide fourni pour la suppression du fichier cloud : :uuid',
            'error' => 'Erreur lors de la suppression du fichier cloud : :uuid',
            'flash_error' => 'Une erreur est survenue lors de la suppression du fichier cloud.',
        ],
        'sync' => [
            'success' => 'Consultation de la page de synchronisation des fichiers cloud',
            'error' => 'Erreur lors de la consultation de la page de synchronisation des fichiers cloud',
            'no_selection' => 'Veuillez sélectionner au moins un fichier à synchroniser.',
            'flash_success' => ':success fichier(s) synchronisé(s) avec succès, :failed en échec.',
            'flash_invalid_argument' => 'Argument invalide fourni pour la synchronisation des fichiers cloud.',
            'flash_error' => 'Une erreur est survenue lors de la synchronisation des fichiers cloud.',
        ],
        'trashed' => [
            'success' => 'Consultation de la liste des fichiers cloud supprimés',
            'error' => 'Erreur lors de la consultation de la liste des fichiers cloud supprimés',
        ],
        'restore' => [
            'success' => 'Restauration du fichier cloud : :name',
            'flash_success' => 'Fichier cloud (:name) restauré avec succès.',
            'warning' => 'Tentative de restauration d\'un fichier cloud introuvable : :uuid',
            'warning_meta' => 'Tentative de restauration d\'un fichier cloud avec un argument invalide : :uuid',
            'error' => 'Erreur lors de la restauration du fichier cloud : :uuid',
            'flash_error' => 'Une erreur est survenue lors de la restauration du fichier cloud.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive du fichier cloud : :name',
            'flash_success' => 'Fichier cloud (:name) supprimé définitivement avec succès.',
            'warning' => 'Tentative de suppression définitive d\'un fichier cloud introuvable : :uuid',
            'warning_meta' => 'Tentative de suppression définitive d\'un fichier cloud avec un argument invalide : :uuid',
            'error' => 'Erreur lors de la suppression définitive du fichier cloud : :uuid',
            'flash_error' => 'Une erreur est survenue lors de la suppression définitive du fichier cloud.',
        ],
    ],
];
