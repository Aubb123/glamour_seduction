<?php

return [
    'comments' => [
        'not_found' => 'Tentative d\'accès à un commentaire de blog introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des commentaires',
            'error' => 'Erreur lors de l\'affichage de la liste des commentaires',
        ],
        'show' => [
            'success' => 'Consultation des détails du commentaire : :content',
            'error' => 'Erreur lors de la consultation du commentaire : :uuid',
        ],
        'activate' => [
            'success' => 'Approbation du commentaire : :content',
            'error' => 'Erreur lors de l\'approbation du commentaire : :uuid',
            'flash_success' => 'Commentaire (:content) approuvé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning_meta' => 'Erreur métier lors de l\'approbation du commentaire : :uuid',
        ],
        'deactivate' => [
            'success' => 'Désapprobation du commentaire : :content',
            'error' => 'Erreur lors de la désapprobation du commentaire : :uuid',
            'flash_success' => 'Commentaire (:content) désapprouvé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
            'warning_meta' => 'Erreur métier lors de la désapprobation du commentaire : :uuid',
        ],
        'destroy' => [
            'success' => 'Suppression du commentaire en base de données : :content',
            'warning_meta' => 'Erreur métier lors de la suppression du commentaire : :uuid',
            'error' => 'Erreur lors de la suppression du commentaire : :uuid',
            'flash_success' => 'Commentaire (:content) supprimé avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des commentaires supprimés',
            'error' => 'Erreur lors de l\'affichage de la liste des commentaires supprimés',
        ],
        'restore' => [
            'success' => 'Restauration du commentaire : :content',
            'warning' => 'Tentative de restauration d\'un commentaire inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration du commentaire : :uuid',
            'error' => 'Erreur lors de la restauration du commentaire : :uuid',
            'flash_success' => 'Commentaire (:content) restauré avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive du commentaire : :uuid',
            'warning' => 'Tentative de suppression définitive d\'un commentaire inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive du commentaire : :uuid',
            'error' => 'Erreur lors de la suppression définitive du commentaire : :uuid',
            'flash_success' => 'Commentaire supprimé définitivement avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'audits' => [
            'success' => 'Consultation des audits du commentaire : :content',
            'error' => 'Erreur lors de la consultation des audits du commentaire : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Consultation des journaux d\'activité du commentaire : :content',
            'error' => 'Erreur lors de la consultation des journaux d\'activité du commentaire : :uuid',
        ],
        'replies' => [
            'success' => 'Consultation des réponses du commentaire : :content',
            'error' => 'Erreur lors de la consultation des réponses du commentaire : :uuid',
        ],
    ],
];
