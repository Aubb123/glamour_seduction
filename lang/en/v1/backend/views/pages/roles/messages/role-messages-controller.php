<?php

return [
    'roles' => [
        'index' => [
            'success' => 'Role list retrieved successfully.',
            'error' => 'An error occurred while retrieving the role list.',
        ],
        'show' => [
            'success' => 'Details of role ":name" retrieved successfully.',
            'warning' => 'The role with UUID ":uuid" was not found.',
            'error' => 'An error occurred while retrieving the details of role with UUID ":uuid".',
            'no_name' => 'Role without name',
            'slug_mismatch' => 'Slug mismatch for role',
        ],
        'edit' => [
            'success' => 'Access to the edit page of role :name',
            'warning' => 'Attempt to edit a non-existent role : :uuid',
            'error' => 'Error while accessing the edit page of role : :uuid',
        ],
        'update' => [
            'success' => 'Role updated in database : :name',
            'warning' => 'Attempt to update a non-existent role : :uuid',
            'warning_meta' => 'Business error while updating the role : :uuid',
            'error' => 'Error while updating the role : :uuid',
            'flash_success' => 'Role (:name) updated successfully.',
            'flash_warning' => 'Role not found.',
            'flash_error' => 'An error occurred.',
        ],
        'activate' => [
            'success' => 'Role :name activated.',
            'warning' => 'Attempt to activate a non-existent role : :uuid',
            'warning_meta' => 'Business error while activating the role : :uuid',
            'error' => 'Error while activating the role : :uuid',
            'flash_success' => 'Role (:name) activated successfully.',
            'flash_warning' => 'Role not found.',
            'flash_error' => 'An error occurred while activating the role.',
        ],
        'deactivate' => [
            'success' => 'Role :name deactivated.',
            'warning' => 'Attempt to deactivate a non-existent role : :uuid',
            'warning_meta' => 'Business error while deactivating the role : :uuid',
            'error' => 'Error while deactivating the role : :uuid',
            'flash_success' => 'Role (:name) deactivated successfully.',
            'flash_warning' => 'Role not found.',
            'flash_error' => 'An error occurred while deactivating the role.',
        ],
        'trashed' => [
            'success' => 'Trashed role list retrieved successfully.',
            'error' => 'An error occurred while retrieving the trashed role list.',
        ],
        'not_found' => 'Attempting to access a non-existent role (UUID: :uuid).',
        'permissions' => [
            'success' => 'Viewing permissions linked to role: :name',
            'warning' => 'Attempt to view permissions for a non-existent role: :uuid',
            'error' => 'Error viewing permissions for role: :uuid',
            'slug_mismatch' => 'Slug mismatch while accessing role permissions',
        ],
        'users' => [
            'success' => 'Viewing users linked to role: :name',
            'warning' => 'Attempt to view users for a non-existent role: :uuid',
            'error' => 'Error viewing users for role: :uuid',
            'slug_mismatch' => 'Slug mismatch while accessing role users',
        ],
        'audits' => [
            'success' => 'Viewing audits linked to role: :name',
            'warning' => 'Attempt to view audits for a non-existent role: :uuid',
            'error' => 'Error viewing audits for role: :uuid',
            'slug_mismatch' => 'Slug mismatch while accessing role audits',
        ],
        'activity_logs' => [
            'success' => 'Viewing activity logs linked to role: :name',
            'warning' => 'Attempt to view activity logs for a non-existent role: :uuid',
            'error' => 'Error viewing activity logs for role: :uuid',
            'slug_mismatch' => 'Slug mismatch while accessing role activity logs',
        ],

        'translations' => [
            'not_found' => 'Attempting to access a non-existent role translation (UUID: :uuid).',
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
                'accessed' => 'Accessed the trash of translations for role ":name".',
                'not_found' => 'Role not found. Cannot access the translation trash (UUID: :uuid).',
                'error' => 'An error occurred while accessing the translation trash of the role (UUID: :uuid).',
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
