<?php

return [

    'countries' => [

        'not_found' => 'Attempt to access a non-existent country (UUID: :uuid).',

        // ─────────────────────────────────────────────
        // INDEX
        // ─────────────────────────────────────────────
        'index' => [
            'success' => 'Country list viewed.',
            'error' => 'Error while retrieving the country list.',
        ],

        // ─────────────────────────────────────────────
        // SHOW
        // ─────────────────────────────────────────────
        'show' => [
            'success' => 'Details of country ":name" viewed.',
            'warning' => 'Attempt to view a non-existent country (UUID: :uuid).',
            'error' => 'Error while retrieving country details (UUID: :uuid).',
            'no_name' => 'No name',
            'slug_mismatch' => 'Slug mismatch for country',
        ],

        // ─────────────────────────────────────────────
        // USERS
        // ─────────────────────────────────────────────
        'users' => [
            'slug_mismatch' => 'Slug mismatch for country',
            'success' => 'Viewing users for country: :name',
            'warning' => 'Attempt to view users for a non-existent country: :uuid',
            'error' => 'Error viewing users for country: :uuid',
        ],

        // ─────────────────────────────────────────────
        // EDIT
        // ─────────────────────────────────────────────
        'edit' => [
            'success' => 'Access to the edit page of country ":name".',
            'warning' => 'Attempt to edit a non-existent country (UUID: :uuid).',
            'error' => 'Error while accessing the country edit page (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // UPDATE
        // ─────────────────────────────────────────────
        'update' => [
            'success' => 'Country ":name" has been updated successfully.',
            'warning' => 'Attempt to update a non-existent country (UUID: :uuid).',
            'warning_meta' => 'Business error while updating the country (UUID: :uuid).',
            'error' => 'Error while updating the country (UUID: :uuid).',
            'flash_success' => 'Country ":name" has been updated successfully.',
            'flash_warning' => 'Country not found.',
            'flash_error' => 'An error occurred while updating the country. Please try again.',
        ],

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'success' => 'Country ":name" has been activated successfully.',
            'warning' => 'Attempt to activate a non-existent country (UUID: :uuid).',
            'error' => 'Error while activating the country (UUID: :uuid).',
            'flash_success' => 'Country ":name" has been activated successfully.',
            'flash_warning' => 'Country not found.',
            'flash_error' => 'An error occurred while activating the country. Please try again.',
            'warning_meta' => 'Business error while activating the country (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'success' => 'Country ":name" has been deactivated successfully.',
            'warning' => 'Attempt to deactivate a non-existent country (UUID: :uuid).',
            'error' => 'Error while deactivating the country (UUID: :uuid).',
            'flash_success' => 'Country ":name" has been deactivated successfully.',
            'flash_warning' => 'Country not found.',
            'flash_error' => 'An error occurred while deactivating the country. Please try again.',
            'warning_meta' => 'Business error while deactivating the country (UUID: :uuid).',
        ],
        'trashed' => [
            'success' => 'Trashed country list retrieved successfully.',
            'error' => 'An error occurred while retrieving the trashed country list.',
        ],
        'audits' => [
            'slug_mismatch' => 'Slug mismatch for country',
            'success' => 'Viewing audits of country :name',
            'error' => 'Error while viewing audits of country : :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Slug mismatch for country',
            'success' => 'Viewing activity logs of country :name',
            'error' => 'Error while viewing activity logs of country : :uuid',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        // ─────────────────────────────────────────────
        // TRANSLATIONS DESTROY
        // ─────────────────────────────────────────────
        'translations' => [
            'not_found' => 'Attempt to access a non-existent country translation (UUID: :uuid).',
            'destroy' => [
                'success' => 'Country translation has been deleted successfully.',
                'warning' => 'Attempt to delete a non-existent country translation (UUID: :uuid).',
                'warning_meta' => 'Business error while deleting the country translation (UUID: :uuid).',
                'error' => 'Error while deleting the country translation (UUID: :uuid).',
                'flash_success' => 'Country translation has been deleted successfully.',
                'flash_warning' => 'Country translation not found.',
                'flash_error' => 'An error occurred while deleting the country translation. Please try again.',
            ],
            'trashed' => [
                'accessed' => 'Accessed the trash of translations for country ":name".',
                'not_found' => 'Country not found. Cannot access the translation trash (UUID: :uuid).',
                'error' => 'An error occurred while accessing the translation trash of the country (UUID: :uuid). Please try again.',
            ],
            'restore' => [
                'success' => 'Country translation has been restored successfully.',
                'warning' => 'Attempt to restore a non-existent country translation (UUID: :uuid).',
                'warning_meta' => 'Business error while restoring the country translation (UUID: :uuid).',
                'error' => 'Error while restoring the country translation (UUID: :uuid).',
                'flash_success' => 'Country translation has been restored successfully.',
                'flash_warning' => 'Country translation not found.',
                'flash_error' => 'An error occurred while restoring the country translation. Please try again.',
            ],
            'force_destroy' => [
                'success' => 'Country translation has been permanently deleted successfully.',
                'warning' => 'Attempt to permanently delete a non-existent country translation (UUID: :uuid).',
                'error' => 'Error while permanently deleting the country translation (UUID: :uuid).',
                'flash_success' => 'Country translation has been permanently deleted successfully.',
                'flash_warning' => 'Country translation not found.',
                'flash_error' => 'An error occurred while permanently deleting the country translation. Please try again.',
                'warning_meta' => 'Business error while permanently deleting the country translation (UUID: :uuid).',
            ],
        ],

    ],

];
