<?php

return [
    'audits' => [
        'not_found' => 'Tentative d\'accès à un audit introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des audits',
            'error' => 'Erreur lors de l\'affichage de la liste des audits',
        ],
        'show' => [
            'success' => 'Consultation des détails de l\'audit',
            'warning' => 'Tentative de consultation d\'un audit inexistant : :uuid',
            'error' => 'Erreur lors de la consultation de l\'audit : :uuid',
        ],
        'destroy' => [
            'success' => 'Suppression de l\'audit en base de données',
            'warning' => 'Tentative de suppression d\'un audit inexistant : :uuid',
            'error' => 'Erreur lors de la suppression de l\'audit : :uuid',
            'flash_success' => 'Audit supprimé avec succès.',
            'flash_warning' => 'Audit introuvable.',
            'flash_error' => 'Une erreur est survenue.',
            'flash_info' => 'Impossible de supprimer l\'audit. Veuillez contacter le développeur du système.',
        ],
        'clear' => [
            'success' => 'Nettoyage des anciens audits : :count supprimés',
            'error' => 'Erreur lors du nettoyage des anciens audits',
            'flash_success' => ':count audit(s) ancien(s) supprimé(s) avec succès.',
            'flash_error' => 'Une erreur est survenue lors du nettoyage des audits.',
            'flash_info' => 'Impossible de nettoyer les audits. Veuillez contacter le développeur du système.',
        ],
    ],
];
