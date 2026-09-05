<?php

return [
    'contacts' => [
        'not_found' => 'Tentative d\'accès à un message de contact introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Affichage de la liste des messages reçus',
            'error' => 'Erreur lors de l\'affichage de la liste des messages reçus',
        ],
        'show' => [
            'success' => 'Consultation des détails du message de : :name',
            'warning' => 'Tentative de consultation d\'un message inexistant : :uuid',
            'error' => 'Erreur lors de la consultation du message : :uuid',
        ],
        'mark_as_read' => [
            'success' => 'Message marqué comme lu : :name',
            'warning' => 'Tentative de marquage comme lu d\'un message inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors du marquage comme lu du message : :uuid',
            'error' => 'Erreur lors du marquage comme lu du message : :uuid',
            'flash_success' => 'Message marqué comme lu avec succès.',
            'flash_warning' => 'Message introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'mark_as_unread' => [
            'success' => 'Message marqué comme non lu : :name',
            'warning' => 'Tentative de marquage comme non lu d\'un message inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors du marquage comme non lu du message : :uuid',
            'error' => 'Erreur lors du marquage comme non lu du message : :uuid',
            'flash_success' => 'Message marqué comme non lu avec succès.',
            'flash_warning' => 'Message introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'destroy' => [
            'success' => 'Suppression du message en base de données : :name',
            'warning' => 'Tentative de suppression d\'un message inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression du message : :uuid',
            'error' => 'Erreur lors de la suppression du message : :uuid',
            'flash_success' => 'Message (:name) supprimé avec succès.',
            'flash_warning' => 'Message introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'trashed' => [
            'success' => 'Affichage de la liste des messages supprimés',
            'error' => 'Erreur lors de l\'affichage de la liste des messages supprimés',
        ],
        'restore' => [
            'success' => 'Restauration du message : :name',
            'warning' => 'Tentative de restauration d\'un message inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la restauration du message : :uuid',
            'error' => 'Erreur lors de la restauration du message : :uuid',
            'flash_success' => 'Message (:name) restauré avec succès.',
            'flash_warning' => 'Message introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
        'force_destroy' => [
            'success' => 'Suppression définitive du message : :name',
            'warning' => 'Tentative de suppression définitive d\'un message inexistant : :uuid',
            'warning_meta' => 'Erreur métier lors de la suppression définitive du message : :uuid',
            'error' => 'Erreur lors de la suppression définitive du message : :uuid',
            'flash_success' => 'Message supprimé définitivement avec succès.',
            'flash_warning' => 'Message introuvable.',
            'flash_error' => 'Une erreur est survenue.',
        ],
    ],
];
