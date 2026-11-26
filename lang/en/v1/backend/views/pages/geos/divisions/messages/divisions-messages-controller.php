<?php

return [

    'divisions' => [

        'not_found' => 'Attempt to access a division that could not be found (UUID: :uuid).',

        'index' => [
            'success' => 'Divisions list retrieved.',
            'error' => 'Error while retrieving the divisions list.',
        ],

        'show' => [
            'success' => 'Details of the division ":name" retrieved.',
            'warning' => 'Attempt to view a non-existent division (UUID: :uuid).',
            'error' => 'Error while retrieving the division details (UUID: :uuid).',
            'slug_mismatch' => 'Slug mismatch for the division',
            'no_name' => 'No name',
        ],

        'create' => [
            'success' => 'Access to the division creation page.',
            'warning' => 'Attempt to create a non-existent division.',
            'error' => 'Error while accessing the division creation page.',
        ],

        'store' => [
            'success' => 'The division ":name" has been created successfully.',
            'warning' => 'Business error while creating the division.',
            'error' => 'Error while creating the division.',
            'flash_success' => 'The division ":name" has been created successfully.',
            'flash_error' => 'An error occurred while creating the division. Please try again.',
        ],

        'edit' => [
            'success' => 'Access to the division edit page ":name".',
            'warning' => 'Attempt to edit a non-existent division (UUID: :uuid).',
            'error' => 'Error while accessing the division edit page (UUID: :uuid).',
        ],

        'update' => [
            'success' => 'The division ":name" has been updated successfully.',
            'warning' => 'Attempt to update a non-existent division (UUID: :uuid).',
            'warning_meta' => 'Business error while updating the division (UUID: :uuid).',
            'error' => 'Error while updating the division (UUID: :uuid).',
            'flash_success' => 'The division ":name" has been updated successfully.',
            'flash_warning' => 'Division not found.',
            'flash_error' => 'An error occurred while updating the division. Please try again.',
        ],

        'activate' => [
            'success' => 'The division ":name" has been activated successfully.',
            'warning' => 'Attempt to activate a non-existent division (UUID: :uuid).',
            'error' => 'Error while activating the division (UUID: :uuid).',
            'flash_success' => 'The division ":name" has been activated successfully.',
            'flash_warning' => 'Division not found.',
            'flash_error' => 'An error occurred while activating the division. Please try again.',
            'warning_meta' => 'Business error while activating the division (UUID: :uuid).',
        ],

        'deactivate' => [
            'success' => 'The division ":name" has been deactivated successfully.',
            'warning' => 'Attempt to deactivate a non-existent division (UUID: :uuid).',
            'error' => 'Error while deactivating the division (UUID: :uuid).',
            'flash_success' => 'The division ":name" has been deactivated successfully.',
            'flash_warning' => 'Division not found.',
            'flash_error' => 'An error occurred while deactivating the division. Please try again.',
            'warning_meta' => 'Business error while deactivating the division (UUID: :uuid).',
        ],

        'destroy' => [
            'success' => 'The division has been deleted successfully.',
            'warning' => 'Attempt to delete a non-existent division (UUID: :uuid).',
            'warning_meta' => 'Business error while deleting the division (UUID: :uuid).',
            'error' => 'Error while deleting the division (UUID: :uuid).',
            'flash_success' => 'The division has been deleted successfully.',
            'flash_warning' => 'Division not found.',
            'flash_error' => 'An error occurred while deleting the division. Please try again.',
        ],

        'trashed' => [
            'success' => 'Deleted divisions list retrieved successfully.',
            'error' => 'An error occurred while retrieving the deleted divisions list.',
        ],

        'restore' => [
            'success' => 'The division has been restored successfully.',
            'warning' => 'Attempt to restore a non-existent division (UUID: :uuid).',
            'warning_meta' => 'Business error while restoring the division (UUID: :uuid).',
            'not_found' => 'Division not found.',
            'error' => 'Error while restoring the division (UUID: :uuid).',
            'flash_success' => 'The division has been restored successfully.',
            'flash_warning' => 'Division not found.',
            'flash_error' => 'An error occurred while restoring the division. Please try again.',
        ],

        'force_destroy' => [
            'success' => 'The division has been permanently deleted successfully.',
            'warning' => 'Attempt to permanently delete a non-existent division (UUID: :uuid).',
            'error' => 'Error while permanently deleting the division (UUID: :uuid).',
            'not_found' => 'Division not found.',
            'flash_success' => 'The division has been permanently deleted successfully.',
            'flash_warning' => 'Division not found.',
            'flash_error' => 'An error occurred while permanently deleting the division. Please try again.',
            'warning_meta' => 'Business error while permanently deleting the division (UUID: :uuid).',
        ],

        'audits' => [
            'success' => 'Consultation of the division audits :name',
            'error' => 'Error while consulting the division audits : :uuid',
        ],

        'activity_logs' => [
            'success' => 'Consultation of the division activity logs :name',
            'error' => 'Error while consulting the division activity logs : :uuid',
        ],

        'children' => [
            'success' => 'Consultation of the sub-divisions of the division :name',
            'error' => 'Error while consulting the sub-divisions of the division : :uuid',
            'slug_mismatch' => 'Slug mismatch for the division',
        ],

        'translations' => [

            'not_found' => 'Attempt to access a division translation that could not be found (UUID: :uuid).',

            'destroy' => [
                'success' => 'The division translation has been deleted successfully.',
                'warning' => 'Attempt to delete a non-existent division translation (UUID: :uuid).',
                'warning_meta' => 'Business error while deleting the division translation (UUID: :uuid).',
                'error' => 'Error while deleting the division translation (UUID: :uuid).',
                'flash_success' => 'The division translation has been deleted successfully.',
                'flash_warning' => 'Division translation not found.',
                'flash_error' => 'An error occurred while deleting the division translation. Please try again.',
            ],

            'trashed' => [
                'accessed' => 'Access to the trash of the division translations ":name".',
                'not_found' => 'Division not found. Unable to access the translations trash (UUID: :uuid).',
                'error' => 'An error occurred while accessing the division translations trash (UUID: :uuid). Please try again.',
            ],

            'restore' => [
                'success' => 'The division translation has been restored successfully.',
                'warning' => 'Attempt to restore a non-existent division translation (UUID: :uuid).',
                'warning_meta' => 'Business error while restoring the division translation (UUID: :uuid).',
                'not_found' => 'Division translation not found.',
                'error' => 'Error while restoring the division translation (UUID: :uuid).',
                'flash_success' => 'The division translation has been restored successfully.',
                'flash_warning' => 'Division translation not found.',
                'flash_error' => 'An error occurred while restoring the division translation. Please try again.',
            ],

            'force_destroy' => [
                'success' => 'The division translation has been permanently deleted successfully.',
                'warning' => 'Attempt to permanently delete a non-existent division translation (UUID: :uuid).',
                'error' => 'Error while permanently deleting the division translation (UUID: :uuid).',
                'not_found' => 'Division translation not found.',
                'flash_success' => 'The division translation has been permanently deleted successfully.',
                'flash_warning' => 'Division translation not found.',
                'flash_error' => 'An error occurred while permanently deleting the division translation. Please try again.',
                'warning_meta' => 'Business error while permanently deleting the division translation (UUID: :uuid).',
            ],
        ],
    ],
];
