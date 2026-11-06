<?php

return [
    'warehouseUsers' => [
        'store' => ['success' => 'Affectation d\'un membre du personnel à l\'entrepôt : :name', 'error' => 'Erreur lors de l\'affectation du membre du personnel', 'not_found' => 'Utilisateur ou poste introuvable.', 'flash_success' => 'Membre du personnel (:name) affecté avec succès.', 'flash_error' => 'Une erreur est survenue.'],
        'update' => ['success' => 'Mise à jour de l\'affectation : :name', 'warning_meta' => 'Erreur métier lors de la mise à jour de l\'affectation : :uuid', 'error' => 'Erreur lors de la mise à jour de l\'affectation : :uuid', 'flash_success' => 'Affectation (:name) mise à jour avec succès.', 'flash_error' => 'Une erreur est survenue.'],
        'destroy' => ['success' => 'Retrait d\'un membre du personnel : :name', 'error' => 'Erreur lors du retrait du membre du personnel : :uuid', 'flash_success' => 'Membre du personnel (:name) retiré avec succès.', 'flash_error' => 'Une erreur est survenue.'],
    ],
];
