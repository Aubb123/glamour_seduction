<?php

return [
    'replies' => [
        'not_found' => 'Tentative d\'accès à une réponse de commentaire de blog introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des réponses aux commentaires',
            'error' => 'Erreur lors de l\'affichage de la liste des réponses aux commentaires',
        ],
        'show' => [
            'success' => 'Consultation des détails de la réponse : :content',
            'error' => 'Erreur lors de la consultation de la réponse : :uuid',
        ],
        'activate' => [
            'success' => 'Approbation de la réponse : :content',
            'error' => 'Erreur lors de l\'approbation de la réponse : :uuid',
            'flash_success' => 'Réponse (:content) approuvée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning_meta' => 'Erreur métier lors de l\'approbation de la réponse : :uuid',
        ],
        'deactivate' => [
            'success' => 'Désapprobation de la réponse : :content',
            'error' => 'Erreur lors de la désapprobation de la réponse : :uuid',
            'flash_success' => 'Réponse (:content) désapprouvée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning_meta' => 'Erreur métier lors de la désapprobation de la réponse : :uuid',
        ],
        'destroy' => [
            'success' => 'Suppression de la réponse en base de données : :content',
            'warning_meta' => 'Erreur métier lors de la suppression de la réponse : :uuid',
            'error' => 'Erreur lors de la suppression de la réponse : :uuid',
            'flash_success' => 'Réponse (:content) supprimée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des réponses supprimées',
            'error' => 'Erreur lors de l\'affichage de la liste des réponses supprimées',
        ],
        'restore' => [
            'success' => 'Restauration de la réponse : :content',
            'warning' => 'Tentative de restauration d\'une réponse inexistante : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration de la réponse : :uuid',
            'error' => 'Erreur lors de la restauration de la réponse : :uuid',
            'flash_success' => 'Réponse (:content) restaurée avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive de la réponse : :uuid',
            'warning' => 'Tentative de suppression définitive d\'une réponse inexistante : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive de la réponse : :uuid',
            'error' => 'Erreur lors de la suppression définitive de la réponse : :uuid',
            'flash_success' => 'Réponse supprimée définitivement avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'audits' => [
            'success' => 'Consultation des audits de la réponse : :content',
            'error' => 'Erreur lors de la consultation des audits de la réponse : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité de la réponse : :content',
            'error' => 'Erreur lors de la consultation des journaux d\'activité de la réponse : :uuid',
        ],
    ],
];
