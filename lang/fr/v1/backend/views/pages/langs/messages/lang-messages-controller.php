<?php

return [
    'langs' => [
        'not_found' => 'Tentative d\'accès à une langue introuvable (UUID: :uuid).',
        'index' => [
            'success' => 'Consultation de la liste des langues',
            'error' => 'Erreur lors de la consultation de la liste des langues',
        ],

        'activate' => [
            'success' => 'Activation de la langue :name',
            'flash_success' => 'La langue :name a été activée avec succès.',
            'warning' => 'Tentative d\'activation d\'une langue introuvable (UUID: :uuid)',
            'flash_warning' => 'Langue non trouvée.',
            'error' => 'Erreur lors de l\'activation de la langue (UUID: :uuid)',
            'flash_error' => 'Une erreur est survenue lors de l\'activation de la langue.',
        ],

        'deactivate' => [
            'success' => 'Désactivation de la langue :name',
            'flash_success' => 'La langue :name a été désactivée avec succès.',
            'warning' => 'Tentative de désactivation d\'une langue introuvable (UUID: :uuid)',
            'flash_warning' => 'Langue non trouvée.',
            'warning_meta' => 'La langue par défaut ne peut pas être désactivée (UUID: :uuid)',
            'error' => 'Erreur lors de la désactivation de la langue (UUID: :uuid)',
            'flash_error' => 'Une erreur est survenue lors de la désactivation de la langue.',
        ],

        'switch' => [
            'success' => 'Changement de langue vers :name',
            'warning_meta' => 'La langue :name n\'est pas active',
            'error' => 'Erreur lors du changement de langue (UUID: :uuid).',
            'flash_error' => 'Une erreur est survenue lors du changement de langue.',
        ],
    ],
];
