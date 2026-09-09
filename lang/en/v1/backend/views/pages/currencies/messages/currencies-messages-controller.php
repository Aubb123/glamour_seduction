<?php

return [

    'currencies' => [

        'not_found' => 'Attempt to access a non-existent currency (UUID: :uuid).',

        // ─────────────────────────────────────────────
        // INDEX
        // ─────────────────────────────────────────────
        'index' => [
            'success' => 'Currency list viewed.',
            'error' => 'Error retrieving the currency list.',
        ],

        // ─────────────────────────────────────────────
        // SHOW
        // ─────────────────────────────────────────────
        'show' => [
            'success' => 'Details of currency ":name" viewed.',
            'warning' => 'Attempt to view a non-existent currency (UUID: :uuid).',
            'error' => 'Error retrieving currency details (UUID: :uuid).',
            'no_name' => 'No name',
            'slug_mismatch' => 'Slug mismatch for the currency',
        ],

        // ─────────────────────────────────────────────
        // COUNTRIES
        // ─────────────────────────────────────────────
        'countries' => [
            'slug_mismatch' => 'Slug mismatch for the currency',
            'success' => 'Countries using the currency viewed: :name',
            'warning' => 'Attempt to view countries of a non-existent currency: :uuid',
            'error' => 'Error viewing countries of the currency: :uuid',
        ],

        // ─────────────────────────────────────────────
        // AUDITS
        // ─────────────────────────────────────────────
        'audits' => [
            'slug_mismatch' => 'Slug mismatch for the currency',
            'success' => 'Audits of the currency viewed: :name',
            'error' => 'Error viewing audits of the currency: :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Slug mismatch for the currency',
            'success' => 'Activity logs of the currency viewed: :name',
            'error' => 'Error viewing activity logs of the currency: :uuid',
        ],

        // ─────────────────────────────────────────────
        // EDIT
        // ─────────────────────────────────────────────
        'edit' => [
            'success' => 'Access to the currency edit page ":name".',
            'warning' => 'Attempt to edit a non-existent currency (UUID: :uuid).',
            'error' => 'Error accessing the currency edit page (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // UPDATE
        // ─────────────────────────────────────────────
        'update' => [
            'success' => 'The currency ":name" has been successfully updated.',
            'warning' => 'Attempt to update a non-existent currency (UUID: :uuid).',
            'warning_meta' => 'Business error while updating the currency (UUID: :uuid).',
            'error' => 'Error updating the currency (UUID: :uuid).',
            'flash_success' => 'The currency ":name" has been successfully updated.',
            'flash_warning' => 'Currency not found.',
            'flash_error' => 'An error occurred while updating the currency. Please try again.',
        ],

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'success' => 'The currency ":name" has been successfully activated.',
            'warning' => 'Attempt to activate a non-existent currency (UUID: :uuid).',
            'error' => 'Error activating the currency (UUID: :uuid).',
            'flash_success' => 'The currency ":name" has been successfully activated.',
            'flash_warning' => 'Currency not found.',
            'flash_error' => 'An error occurred while activating the currency. Please try again.',
            'warning_meta' => 'Business error while activating the currency (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'success' => 'The currency ":name" has been successfully deactivated.',
            'warning' => 'Attempt to deactivate a non-existent currency (UUID: :uuid).',
            'error' => 'Error deactivating the currency (UUID: :uuid).',
            'flash_success' => 'The currency ":name" has been successfully deactivated.',
            'flash_warning' => 'Currency not found.',
            'flash_error' => 'An error occurred while deactivating the currency. Please try again.',
            'warning_meta' => 'Business error while deactivating the currency (UUID: :uuid).',
        ],

        // ─────────────────────────────────────────────
        // DESTROY
        // ─────────────────────────────────────────────
        'destroy' => [
            'success' => 'The currency ":name" has been successfully deleted.',
            'warning' => 'Attempt to delete a non-existent currency (UUID: :uuid).',
            'warning_meta' => 'Business error while deleting the currency (UUID: :uuid).',
            'error' => 'Error deleting the currency (UUID: :uuid).',
            'flash_success' => 'The currency ":name" has been successfully deleted.',
            'flash_warning' => 'Currency not found.',
            'flash_error' => 'An error occurred while deleting the currency. Please try again.',
        ],

        // ─────────────────────────────────────────────
        // TRASHED
        // ─────────────────────────────────────────────
        'trashed' => [
            'success' => 'Deleted currency list viewed.',
            'error' => 'Error retrieving the deleted currency list.',
        ],

        // ─────────────────────────────────────────────
        // RESTORE
        // ─────────────────────────────────────────────
        'restore' => [
            'success' => 'The currency ":name" has been successfully restored.',
            'warning' => 'Attempt to restore a non-existent currency (UUID: :uuid).',
            'warning_meta' => 'Business error while restoring the currency (UUID: :uuid).',
            'error' => 'Error restoring the currency (UUID: :uuid).',
            'flash_success' => 'The currency ":name" has been successfully restored.',
            'flash_warning' => 'Currency not found.',
            'flash_error' => 'An error occurred while restoring the currency. Please try again.',
        ],

        // ─────────────────────────────────────────────
        // FORCE DESTROY
        // ─────────────────────────────────────────────
        'force_destroy' => [
            'success' => 'The currency ":name" has been successfully permanently deleted.',
            'warning' => 'Attempt to permanently delete a non-existent currency (UUID: :uuid).',
            'warning_meta' => 'Business error while permanently deleting the currency (UUID: :uuid).',
            'error' => 'Error permanently deleting the currency (UUID: :uuid).',
            'flash_success' => 'The currency ":name" has been successfully permanently deleted.',
            'flash_warning' => 'Currency not found.',
            'flash_error' => 'An error occurred while permanently deleting the currency. Please try again.',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────
        'translations' => [
            'not_found' => 'Attempt to access a non-existent currency translation (UUID: :uuid).',
            'destroy' => [
                'success' => 'The currency translation has been successfully deleted.',
                'warning' => 'Attempt to delete a non-existent currency translation (UUID: :uuid).',
                'warning_meta' => 'Business error while deleting the currency translation (UUID: :uuid).',
                'error' => 'Error deleting the currency translation (UUID: :uuid).',
                'flash_success' => 'The currency translation has been successfully deleted.',
                'flash_warning' => 'Currency translation not found.',
                'flash_error' => 'An error occurred while deleting the currency translation. Please try again.',
            ],
            'trashed' => [
                'success' => 'Access to the trash of currency translations ":name".',
                'not_found' => 'Currency not found. Cannot access the translations trash (UUID: :uuid).',
                'error' => 'An error occurred while accessing the currency translations trash (UUID: :uuid). Please try again.',
            ],
            'restore' => [
                'success' => 'The currency translation has been successfully restored.',
                'warning' => 'Attempt to restore a non-existent currency translation (UUID: :uuid).',
                'warning_meta' => 'Business error while restoring the currency translation (UUID: :uuid).',
                'error' => 'Error restoring the currency translation (UUID: :uuid).',
                'flash_success' => 'The currency translation has been successfully restored.',
                'flash_warning' => 'Currency translation not found.',
                'flash_error' => 'An error occurred while restoring the currency translation. Please try again.',
            ],
            'force_destroy' => [
                'success' => 'The currency translation has been successfully permanently deleted.',
                'warning' => 'Attempt to permanently delete a non-existent currency translation (UUID: :uuid).',
                'warning_meta' => 'Business error while permanently deleting the currency translation (UUID: :uuid).',
                'error' => 'Error permanently deleting the currency translation (UUID: :uuid).',
                'flash_success' => 'The currency translation has been successfully permanently deleted.',
                'flash_warning' => 'Currency translation not found.',
                'flash_error' => 'An error occurred while permanently deleting the currency translation. Please try again.',
            ],
        ],

    ],

];
