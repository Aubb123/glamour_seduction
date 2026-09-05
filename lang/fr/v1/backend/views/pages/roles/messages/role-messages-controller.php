<?php

return [
    'roles' => [
        'index' => [
            'success' => 'Liste des rôles récupérée avec succès.',
            'error' => 'Une erreur est survenue lors de la récupération de la liste des rôles.',
        ],
        'show' => [
            'success' => 'Détail du rôle ":name" récupéré avec succès.',
            'warning' => 'Le rôle avec l\'UUID ":uuid" est introuvable.',
            'error' => 'Une erreur est survenue lors de la récupération du détail du rôle avec l\'UUID ":uuid".',
            'no_name' => 'Rôle sans nom',
            'slug_mismatch' => 'Incohérence du slug pour le rôle',
        ],
        'edit' => [
            'success' => 'Accès à la page de modification du rôle : :name',
            'warning' => 'Tentative de modification d\'un rôle introuvable : :uuid',
            'error' => 'Erreur lors de l\'accès à la page de modification du rôle : :uuid',
        ],
        'update' => [
            'success' => 'Mise à jour du rôle en base de données : :name',
            'warning' => 'Tentative de mise à jour d\'un rôle introuvable : :uuid',
            'warning_meta' => 'Erreur métier lors de la mise à jour du rôle : :uuid',
            'error' => 'Erreur lors de la mise à jour du rôle : :uuid',
            'flash_success' => 'Rôle (:name) mis à jour avec succès.',
            'flash_warning' => 'Rôle introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'activate' => [
            'success' => 'Activation du rôle : :name',
            'warning' => 'Tentative d\'activation d\'un rôle introuvable : :uuid',
            'warning_meta' => 'Erreur métier lors de l\'activation du rôle : :uuid',
            'error' => 'Erreur lors de l\'activation du rôle : :uuid',
            'flash_success' => 'Rôle (:name) activé avec succès.',
            'flash_warning' => 'Rôle introuvable.',
            'flash_error' => 'Une erreur est survenue lors de l\'activation du rôle.',
        ],
        'deactivate' => [
            'success' => 'Désactivation du rôle : :name',
            'warning' => 'Tentative de désactivation d\'un rôle introuvable : :uuid',
            'warning_meta' => 'Erreur métier lors de la désactivation du rôle : :uuid',
            'error' => 'Erreur lors de la désactivation du rôle : :uuid',
            'flash_success' => 'Rôle (:name) désactivé avec succès.',
            'flash_warning' => 'Rôle introuvable.',
            'flash_error' => 'Une erreur est survenue lors de la désactivation du rôle.',
        ],
        'trashed' => [
            'success' => 'Liste des rôles supprimés récupérée avec succès.',
            'error' => 'Une erreur est survenue lors de la récupération de la liste des rôles supprimés.',
        ],
        'not_found' => 'Tentative d\'accès à un rôle introuvable (UUID: :uuid).',
        'permissions' => [
            'success' => 'Consultation des permissions liées au rôle : :name',
            'warning' => 'Tentative de consultation des permissions d\'un rôle introuvable : :uuid',
            'error' => 'Erreur lors de la consultation des permissions du rôle : :uuid',
            'slug_mismatch' => 'Incohérence du slug pour le rôle lors de l\'accès à ses permissions',
        ],
        'users' => [
            'success' => 'Consultation des utilisateurs liés au rôle : :name',
            'warning' => 'Tentative de consultation des utilisateurs d\'un rôle introuvable : :uuid',
            'error' => 'Erreur lors de la consultation des utilisateurs du rôle : :uuid',
            'slug_mismatch' => 'Incohérence du slug pour le rôle lors de l\'accès à ses utilisateurs',
        ],
        'audits' => [
            'success' => 'Consultation des audits liés au rôle : :name',
            'warning' => 'Tentative de consultation des audits d\'un rôle introuvable : :uuid',
            'error' => 'Erreur lors de la consultation des audits du rôle : :uuid',
            'slug_mismatch' => 'Incohérence du slug pour le rôle lors de l\'accès à ses audits',
        ],
        'activity_logs' => [
            'success' => 'Consultation des logs d\'activité liés au rôle : :name',
            'warning' => 'Tentative de consultation des logs d\'activité d\'un rôle introuvable : :uuid',
            'error' => 'Erreur lors de la consultation des logs d\'activité du rôle : :uuid',
            'slug_mismatch' => 'Incohérence du slug pour le rôle lors de l\'accès à ses logs d\'activité',
        ],

        'translations' => [
            'not_found' => 'Tentative d\'accès à une traduction de rôle introuvable (UUID: :uuid).',
            'destroy' => [
                'success' => 'Suppression de la traduction : :uuid',
                'warning' => 'Tentative de suppression d\'une traduction introuvable : :uuid',
                'warning_meta' => 'Erreur métier lors de la suppression de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'trashed' => [
                'accessed' => 'Accès à la corbeille des traductions du rôle ":name".',
                'not_found' => 'Rôle introuvable. Impossible d\'accéder à la corbeille des traductions (UUID: :uuid).',
                'error' => 'Une erreur est survenue lors de l\'accès à la corbeille des traductions du rôle (UUID: :uuid).',
            ],
            'restore' => [
                'success' => 'Restauration de la traduction : :uuid',
                'warning' => 'Tentative de restauration d\'une traduction introuvable : :uuid',
                'warning_meta' => 'Erreur métier lors de la restauration de la traduction : :uuid',
                'error' => 'Erreur lors de la restauration de la traduction : :uuid',
                'flash_success' => 'Traduction restaurée avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
            'force_destroy' => [
                'success' => 'Suppression définitive de la traduction : :uuid',
                'warning' => 'Tentative de suppression définitive d\'une traduction introuvable : :uuid',
                'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction : :uuid',
                'error' => 'Erreur lors de la suppression définitive de la traduction : :uuid',
                'flash_success' => 'Traduction supprimée définitivement avec succès.',
                'flash_warning' => 'Traduction introuvable.',
                'flash_error' => 'Une erreur est survenue.',
            ],
        ],
    ],
];
