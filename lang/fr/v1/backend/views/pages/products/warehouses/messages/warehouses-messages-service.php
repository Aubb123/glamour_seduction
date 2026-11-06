<?php

return [
    'productWarehouses' => [
        'activate' => [
            'already_active' => 'Cet entrepôt est déjà actif.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cet entrepôt est déjà inactif.',
            'default_forbidden' => 'Impossible de désactiver l\'entrepôt par défaut. Désignez d\'abord un autre entrepôt par défaut.',
        ],
        'update' => [
            'default_forbidden' => 'Impossible de retirer le statut « par défaut » à ce dernier entrepôt par défaut. Désignez d\'abord un autre entrepôt par défaut.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer un entrepôt actif. Veuillez le désactiver d\'abord.',
            'already_deleted' => 'Cet entrepôt est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les entrepôts déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les entrepôts déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],
    ],
    'warehouseUsers' => [
        'store' => [
            'success' => 'Affectation d\'un membre du personnel à l\'entrepôt : :name',
            'error' => 'Erreur lors de l\'affectation du membre du personnel',
            'not_found' => 'Utilisateur ou poste introuvable.',
            'flash_success' => 'Membre du personnel (:name) affecté avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'update' => [
            'success' => 'Mise à jour de l\'affectation : :name',
            'warning_meta' => 'Erreur métier lors de la mise à jour de l\'affectation : :uuid',
            'error' => 'Erreur lors de la mise à jour de l\'affectation : :uuid',
            'flash_success' => 'Affectation (:name) mise à jour avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Retrait d\'un membre du personnel : :name',
            'error' => 'Erreur lors du retrait du membre du personnel : :uuid',
            'flash_success' => 'Membre du personnel (:name) retiré avec succès.',
            'flash_error' => 'Une erreur est survenue.',
        ],
    ],
];
