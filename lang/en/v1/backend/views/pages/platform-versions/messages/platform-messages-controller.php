<?php

return [
    'versions' => [
        'not_found' => 'Attempt to access a non-existent version (UUID: :uuid).',
        'index' => [
            'success' => 'Version list viewed.',
            'error' => 'Error while viewing the version list.',
        ],
        'create' => [
            'success' => 'Access to the version creation page.',
            'error' => 'Error while accessing the version creation page.',
        ],
        'store' => [
            'success' => 'New version created : :uuid',
            'error' => 'Error while creating a version.',
            'flash_success' => 'Version (:name) created successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'show' => [
            'no_name' => 'No name found, no translation available',
            'success' => 'Details of version :name viewed.',
            'warning' => 'Attempt to view a non-existent version : :uuid',
            'error' => 'Error while viewing the version : :uuid',
            'slug_mismatch' => 'Slug mismatch for version',
        ],
        'edit' => [
            'success' => 'Access to the edit page of version :name',
            'warning' => 'Attempt to edit a non-existent version : :uuid',
            'error' => 'Error while accessing the edit page of version : :uuid',
        ],
        'update' => [
            'success' => 'Version updated in database : :name',
            'warning' => 'Attempt to update a non-existent version : :uuid',
            'warning_unpub' => 'Business error while unpublishing the version : :uuid',
            'error' => 'Error while updating the version : :uuid',
            'flash_success' => 'Version (:name) updated successfully.',
            'flash_warning' => 'Version not found.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Version deleted from database : :name',
            'warning' => 'Attempt to delete a non-existent version : :uuid',
            'warning_meta' => 'Business error while deleting the version : :uuid',
            'error' => 'Error while deleting the version : :uuid',
            'flash_success' => 'Version (:name) deleted successfully.',
            'flash_warning' => 'Version not found.',
            'flash_error' => 'An error occurred.',
        ],
        'activate' => [
            'success' => 'Version :name activated.',
            'error' => 'Error while activating the version : :uuid',
            'flash_success' => 'Version (:name) activated successfully.',
            'flash_error' => 'An error occurred.',
            'warning' => 'Attempt to activate a non-existent version : :uuid',
            'flash_warning' => 'Version not found.',
            'warning_meta' => 'Business error while activating the version : :uuid',
        ],
        'deactivate' => [
            'success' => 'Version :name deactivated.',
            'error' => 'Error while deactivating the version : :uuid',
            'flash_success' => 'Version (:name) deactivated successfully.',
            'flash_error' => 'An error occurred.',
            'warning' => 'Attempt to deactivate a non-existent version : :uuid',
            'flash_warning' => 'Version not found.',
            'warning_meta' => 'Business error while deactivating the version : :uuid',
        ],
        'audits' => [
            'slug_mismatch' => 'Slug mismatch for version',
            'success' => 'Viewing audits of version :name',
            'error' => 'Error while viewing audits of version : :uuid',
        ],
        'activity-logs' => [
            'slug_mismatch' => 'Slug mismatch for version',
            'success' => 'Viewing activity logs of version :name',
            'error' => 'Error while viewing activity logs of version : :uuid',
        ],
        'publish' => [
            'success' => 'Version :name published.',
            'error' => 'Error while publishing the version : :uuid',
            'flash_success' => 'Version (:name) published successfully.',
            'flash_error' => 'An error occurred.',
            'warning_meta' => 'Business error while publishing the version : :uuid',
        ],
        'unpublish' => [
            'success' => 'Version :code unpublished.',
            'warning' => 'Business error while unpublishing the version : :uuid',
            'error' => 'Error while unpublishing the version : :uuid',
            'flash_success' => 'Version (:name) unpublished successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Trash list of versions viewed.',
            'error' => 'Error while viewing the trash list of versions.',
        ],
        'restore' => [
            'success' => 'Version :name restored.',
            'warning' => 'Attempt to restore a non-existent version : :uuid',
            'warning_meta' => 'Business error while restoring the version : :uuid',
            'error' => 'Error while restoring the version : :uuid',
            'flash_success' => 'Version (:name) restored successfully.',
            'flash_warning' => 'Version not found.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Version :name permanently deleted.',
            'warning' => 'Attempt to permanently delete a non-existent version : :uuid',
            'warning_meta' => 'Business error while permanently deleting the version : :uuid',
            'error' => 'Error while permanently deleting the version : :uuid',
            'flash_success' => 'Version (:name) permanently deleted successfully.',
            'flash_warning' => 'Version not found.',
            'flash_error' => 'An error occurred.',
            'unknown' => 'Unknown version',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        'translations' => [
            'not_found' => 'Attempt to access a non-existent translation (UUID: :uuid).',
            'destroy' => [
                'success' => 'Translation deleted : :uuid',
                'warning' => 'Attempt to delete a non-existent translation : :uuid',
                'warning_meta' => 'Business error while deleting the translation : :uuid',
                'error' => 'Error while deleting the translation : :uuid',
                'flash_success' => 'Translation deleted successfully.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
            'trashed' => [
                'success' => 'Trash list of translations for version :code viewed.',
                'error' => 'Error while viewing the translations trash.',
            ],
            'restore' => [
                'success' => 'Translation restored : :uuid',
                'warning' => 'Attempt to restore a non-existent translation : :uuid',
                'warning_meta' => 'Business error while restoring the translation : :uuid',
                'error' => 'Error while restoring the translation : :uuid',
                'flash_success' => 'Translation restored successfully.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
            'force_destroy' => [
                'success' => 'Translation permanently deleted : :uuid',
                'warning' => 'Attempt to permanently delete a non-existent translation : :uuid',
                'warning_meta' => 'Business error while permanently deleting the translation : :uuid',
                'error' => 'Error while permanently deleting the translation : :uuid',
                'flash_success' => 'Translation permanently deleted successfully.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],

        ],

    ],
];
