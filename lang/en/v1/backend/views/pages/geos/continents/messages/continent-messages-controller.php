<?php

return [
    'geocontinents' => [
        'not_found' => 'Attempt to access a non-existent continent (UUID: :uuid).',
        'index' => [
            'success' => 'Continent list retrieved successfully.',
            'error' => 'An error occurred while retrieving the continent list.',
        ],
        'show' => [
            'no_name' => 'Continent without name',
            'success' => 'Details of continent :name retrieved successfully.',
            'warning' => 'Continent not found (UUID: :uuid).',
            'error' => 'An error occurred while retrieving the continent (UUID: :uuid).',
            'slug_mismatch' => 'Slug mismatch for continent',
        ],
        'countries' => [
            'slug_mismatch' => 'Slug mismatch for continent',
            'success' => 'Viewing countries for continent: :name',
            'error' => 'Error viewing countries for continent: :uuid',
        ],
        'audits' => [
            'slug_mismatch' => 'Slug mismatch for continent',
            'success' => 'Viewing audits of continent :name',
            'error' => 'Error while viewing audits of continent : :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Slug mismatch for continent',
            'success' => 'Viewing activity logs of continent :name',
            'error' => 'Error while viewing activity logs of continent : :uuid',
        ],
        'activate' => [
            'success' => 'Continent :name activated successfully.',
            'warning' => 'Continent not found (UUID: :uuid).',
            'error' => 'An error occurred while activating the continent (UUID: :uuid).',
            'flash_success' => 'The continent :name has been activated successfully.',
            'flash_error' => 'An error occurred while activating the continent.',
        ],
        'deactivate' => [
            'success' => 'Continent :name deactivated successfully.',
            'warning' => 'Continent not found (UUID: :uuid).',
            'error' => 'An error occurred while deactivating the continent (UUID: :uuid).',
            'flash_success' => 'The continent :name has been deactivated successfully.',
            'flash_error' => 'An error occurred while deactivating the continent.',
        ],
        'trashed' => [
            'success' => 'Trashed continent list retrieved successfully.',
            'error' => 'An error occurred while retrieving the trashed continent list.',
        ],
        'edit' => [
            'success' => 'Access to the edit page of continent :name',
            'warning' => 'Attempt to edit a non-existent continent : :uuid',
            'error' => 'Error while accessing the edit page of continent : :uuid',
        ],
        'update' => [
            'success' => 'Continent updated in database : :name',
            'warning' => 'Attempt to update a non-existent continent : :uuid',
            'warning_meta' => 'Business error while updating the continent : :uuid',
            'error' => 'Error while updating the continent : :uuid',
            'flash_success' => 'Continent (:name) updated successfully.',
            'flash_warning' => 'Continent not found.',
            'flash_error' => 'An error occurred.',
        ],
        'translations' => [
            'not_found' => 'Attempt to access a non-existent continent translation (UUID: :uuid).',
            'destroy' => [
                'success' => 'Continent translation has been deleted successfully.',
                'warning' => 'Attempt to delete a non-existent continent translation (UUID: :uuid).',
                'warning_meta' => 'Business error while deleting the continent translation (UUID: :uuid).',
                'error' => 'Error while deleting the continent translation (UUID: :uuid).',
                'flash_success' => 'Continent translation has been deleted successfully.',
                'flash_warning' => 'Continent translation not found.',
                'flash_error' => 'An error occurred while deleting the continent translation. Please try again.',
            ],
            'trashed' => [
                'success' => 'Accessed the trash of translations for continent ":name".',
                'not_found' => 'Continent not found. Cannot access the translation trash (UUID: :uuid).',
                'error' => 'An error occurred while accessing the translation trash of the continent (UUID: :uuid). Please try again.',
            ],
            'restore' => [
                'success' => 'Continent translation has been restored successfully.',
                'warning' => 'Attempt to restore a non-existent continent translation (UUID: :uuid).',
                'warning_meta' => 'Business error while restoring the continent translation (UUID: :uuid).',
                'error' => 'Error while restoring the continent translation (UUID: :uuid).',
                'flash_success' => 'Continent translation has been restored successfully.',
                'flash_warning' => 'Continent translation not found.',
                'flash_error' => 'An error occurred while restoring the continent translation. Please try again.',
            ],
            'force_destroy' => [
                'success' => 'Continent translation has been permanently deleted successfully.',
                'warning' => 'Attempt to permanently delete a non-existent continent translation (UUID: :uuid).',
                'warning_meta' => 'Business error while permanently deleting the continent translation (UUID: :uuid).',
                'error' => 'Error while permanently deleting the continent translation (UUID: :uuid).',
                'flash_success' => 'Continent translation has been permanently deleted successfully.',
                'flash_warning' => 'Continent translation not found.',
                'flash_error' => 'An error occurred while permanently deleting the continent translation. Please try again.',
            ],
        ],
    ],
];
