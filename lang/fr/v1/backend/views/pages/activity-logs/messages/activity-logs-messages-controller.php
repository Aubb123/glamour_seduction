<?php

return [
    'activity_logs' => [
        'not_found' => 'Tentative d\'accès à un journal d\'activité introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des journaux d\'activité',
            'error' => 'Erreur lors de l\'affichage de la liste des journaux d\'activité',
        ],
        'show' => [
            'success' => 'Consultation des détails du journal d\'activité',
            'warning' => 'Tentative de consultation d\'un journal d\'activité inexistant : :uuid',
            'error' => 'Erreur lors de la consultation du journal d\'activité : :uuid',
        ],
        'destroy' => [
            'flash_info' => 'Impossible de supprimer le journal d\'activité. Veuillez contacter le développeur pour plus d\'informations.',
            'success' => 'Suppression du journal d\'activité en base de données',
            'warning' => 'Tentative de suppression d\'un journal d\'activité inexistant : :uuid',
            'error' => 'Erreur lors de la suppression du journal d\'activité : :uuid',
            'flash_success' => 'Journal d\'activité supprimé avec succès.',
            'flash_warning' => 'Journal d\'activité introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'clear' => [
            'flash_info' => 'Impossible de nettoyer les journaux d\'activité. Veuillez contacter le développeur pour plus d\'informations.',
            'success' => 'Nettoyage des anciens journaux d\'activité : :count supprimés',
            'error' => 'Erreur lors du nettoyage des anciens journaux d\'activité',
            'flash_success' => ':count journal/aux ancien(s) supprimé(s) avec succès.',
            'flash_error' => 'Une erreur est survenue lors du nettoyage des journaux d\'activité.',
        ],
    ],
];
