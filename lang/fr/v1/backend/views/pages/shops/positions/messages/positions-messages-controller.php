<?php

return [
    'positions' => [
        'not_found' => 'Tentative d\'accès à un poste introuvable (UUID: :uuid).',
        'index' => ['success' => 'Affichage de la liste des postes', 'error' => 'Erreur lors de l\'affichage de la liste des postes'],
        'create' => ['success' => 'Accès à la page de création d\'un poste', 'error' => 'Erreur lors de l\'accès à la page de création d\'un poste'],
        'store' => ['success' => 'Création d\'un nouveau poste : :name', 'error' => 'Erreur lors de la création du poste', 'flash_success' => 'Poste (:name) créé avec succès.', 'flash_error' => 'Une erreur est survenue.'],
        'show' => ['success' => 'Consultation des détails du poste : :name', 'warning' => 'Tentative de consultation d\'un poste inexistant : :uuid', 'error' => 'Erreur lors de la consultation du poste : :uuid'],
        'audits' => ['success' => 'Consultation des audits du poste : :name', 'error' => 'Erreur lors de la consultation des audits du poste : :uuid'],
        'activity-logs' => ['success' => 'Consultation des journaux d\'activité du poste : :name', 'error' => 'Erreur lors de la consultation des journaux d\'activité du poste : :uuid'],
        'edit' => ['success' => 'Accès à la page de modification du poste : :name', 'error' => 'Erreur lors de l\'accès à la page de modification du poste : :uuid'],
        'update' => ['success' => 'Mise à jour du poste : :name', 'warning_meta' => 'Erreur métier lors de la mise à jour du poste : :uuid', 'error' => 'Erreur lors de la mise à jour du poste : :uuid', 'flash_success' => 'Poste (:name) mis à jour avec succès.', 'flash_error' => 'Une erreur est survenue.'],
        'activate' => ['success' => 'Activation du poste : :name', 'warning_meta' => 'Erreur métier lors de l\'activation du poste : :uuid', 'error' => 'Erreur lors de l\'activation du poste : :uuid', 'flash_success' => 'Poste (:name) activé avec succès.', 'flash_error' => 'Une erreur est survenue.'],
        'deactivate' => ['success' => 'Désactivation du poste : :name', 'warning_meta' => 'Erreur métier lors de la désactivation du poste : :uuid', 'error' => 'Erreur lors de la désactivation du poste : :uuid', 'flash_success' => 'Poste (:name) désactivé avec succès.', 'flash_error' => 'Une erreur est survenue.'],
        'destroy' => ['success' => 'Suppression du poste : :name', 'warning_meta' => 'Erreur métier lors de la suppression du poste : :uuid', 'error' => 'Erreur lors de la suppression du poste : :uuid', 'flash_success' => 'Poste (:name) supprimé avec succès.', 'flash_error' => 'Une erreur est survenue.'],
        'trashed' => ['success' => 'Affichage de la liste des postes supprimés', 'error' => 'Erreur lors de l\'affichage de la liste des postes supprimés'],
        'restore' => ['success' => 'Restauration du poste : :name', 'warning' => 'Tentative de restauration d\'un poste inexistant : :uuid', 'warning_meta' => 'Erreur métier lors de la restauration du poste : :uuid', 'error' => 'Erreur lors de la restauration du poste : :uuid', 'flash_success' => 'Poste (:name) restauré avec succès.', 'flash_warning' => 'Poste introuvable.', 'flash_error' => 'Une erreur est survenue.'],
        'force_destroy' => ['success' => 'Suppression définitive du poste : :name', 'warning' => 'Tentative de suppression définitive d\'un poste inexistant : :uuid', 'warning_meta' => 'Erreur métier lors de la suppression définitive du poste : :uuid', 'error' => 'Erreur lors de la suppression définitive du poste : :uuid', 'flash_success' => 'Poste supprimé définitivement avec succès.', 'flash_warning' => 'Poste introuvable.', 'flash_error' => 'Une erreur est survenue.'],
        'translations' => [
            'destroy' => ['success' => 'Suppression de la traduction : :uuid', 'warning' => 'Tentative de suppression d\'une traduction introuvable : :uuid', 'warning_meta' => 'Erreur métier lors de la suppression de la traduction : :uuid', 'error' => 'Erreur lors de la suppression de la traduction : :uuid', 'flash_success' => 'Traduction supprimée avec succès.', 'flash_warning' => 'Traduction introuvable.', 'flash_error' => 'Une erreur est survenue.'],
            'trashed' => ['accessed' => 'Affichage de la corbeille des traductions du poste : :name', 'not_found' => 'Poste introuvable (UUID: :uuid).', 'error' => 'Erreur lors de l\'affichage de la corbeille des traductions : :uuid'],
            'restore' => ['success' => 'Restauration de la traduction : :uuid', 'warning' => 'Tentative de restauration d\'une traduction introuvable : :uuid', 'warning_meta' => 'Erreur métier lors de la restauration de la traduction : :uuid', 'error' => 'Erreur lors de la restauration de la traduction : :uuid', 'flash_success' => 'Traduction restaurée avec succès.', 'flash_warning' => 'Traduction introuvable.', 'flash_error' => 'Une erreur est survenue.'],
            'force_destroy' => ['success' => 'Suppression définitive de la traduction : :uuid', 'warning' => 'Tentative de suppression définitive d\'une traduction introuvable : :uuid', 'warning_meta' => 'Erreur métier lors de la suppression définitive de la traduction : :uuid', 'error' => 'Erreur lors de la suppression définitive de la traduction : :uuid', 'flash_success' => 'Traduction supprimée définitivement avec succès.', 'flash_warning' => 'Traduction introuvable.', 'flash_error' => 'Une erreur est survenue.'],
        ],
    ],
];
