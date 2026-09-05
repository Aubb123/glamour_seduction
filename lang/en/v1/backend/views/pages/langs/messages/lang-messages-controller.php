<?php

return [
    'langs' => [
        'not_found' => 'Attempt to access a non-existent language (UUID: :uuid).',
        'index' => [
            'success' => 'Language list viewed.',
            'error' => 'Error while viewing the language list.',
        ],

        'activate' => [
            'success' => 'Language :name activated.',
            'flash_success' => 'The language :name has been activated successfully.',
            'warning' => 'Attempt to activate a non-existent language (UUID: :uuid).',
            'flash_warning' => 'Language not found.',
            'error' => 'Error while activating the language (UUID: :uuid).',
            'flash_error' => 'An error occurred while activating the language.',
        ],

        'deactivate' => [
            'success' => 'Language :name deactivated.',
            'flash_success' => 'The language :name has been deactivated successfully.',
            'warning' => 'Attempt to deactivate a non-existent language (UUID: :uuid).',
            'flash_warning' => 'Language not found.',
            'warning_meta' => 'The default language cannot be deactivated (UUID: :uuid).',
            'error' => 'Error while deactivating the language (UUID: :uuid).',
            'flash_error' => 'An error occurred while deactivating the language.',
        ],

        'switch' => [
            'success' => 'Language switched to :name',
            'warning_meta' => 'The language :name is not active',
            'error' => 'Error while switching the language (UUID: :uuid).',
            'flash_error' => 'An error occurred while switching language.',
        ],
    ],
];
