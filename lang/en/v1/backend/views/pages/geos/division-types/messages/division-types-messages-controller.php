<?php

return [

    'divisionTypes' => [

        'not_found' => 'Attempt to access a non-existent division type (UUID: :uuid).',

        'index' => [
            'success' => 'Division types list retrieved.',
            'error' => 'Error while retrieving the division types list.',
        ],

        'show' => [
            'success' => 'Division type ":name" details retrieved.',
            'warning' => 'Attempt to access a non-existent division type (UUID: :uuid).',
            'error' => 'Error while retrieving division type details (UUID: :uuid).',
            'no_name' => 'No name',
            'slug_mismatch' => 'Slug mismatch for division type',

        ],

        'create' => [
            'success' => 'Access to the division type creation page.',
            'warning' => 'Attempt to access an inexistent division type creation page.',
            'error' => 'Error while accessing the division type creation page.',
        ],

        'store' => [
            'success' => 'Division type ":name" has been created successfully.',
            'warning' => 'Business error while creating the division type.',
            'error' => 'Error while creating the division type.',
            'flash_success' => 'Division type ":name" has been created successfully.',
            'flash_error' => 'An error occurred while creating the division type. Please try again.',
        ],

        'edit' => [
            'success' => 'Access to the division type edit page ":name".',
            'warning' => 'Attempt to edit a non-existent division type (UUID: :uuid).',
            'error' => 'Error while accessing the division type edit page (UUID: :uuid).',
        ],

        'update' => [
            'success' => 'Division type ":name" has been updated successfully.',
            'warning' => 'Attempt to update a non-existent division type (UUID: :uuid).',
            'warning_meta' => 'Business error while updating division type (UUID: :uuid).',
            'error' => 'Error while updating division type (UUID: :uuid).',
            'flash_success' => 'Division type ":name" has been updated successfully.',
            'flash_warning' => 'Division type not found.',
            'flash_error' => 'An error occurred while updating the division type. Please try again.',
        ],

        'activate' => [
            'success' => 'Division type ":name" has been activated successfully.',
            'warning' => 'Attempt to activate a non-existent division type (UUID: :uuid).',
            'error' => 'Error while activating division type (UUID: :uuid).',
            'flash_success' => 'Division type ":name" has been activated successfully.',
            'flash_warning' => 'Division type not found.',
            'flash_error' => 'An error occurred while activating the division type. Please try again.',
            'warning_meta' => 'Business error while activating division type (UUID: :uuid).',
        ],

        'deactivate' => [
            'success' => 'Division type ":name" has been deactivated successfully.',
            'warning' => 'Attempt to deactivate a non-existent division type (UUID: :uuid).',
            'error' => 'Error while deactivating division type (UUID: :uuid).',
            'flash_success' => 'Division type ":name" has been deactivated successfully.',
            'flash_warning' => 'Division type not found.',
            'flash_error' => 'An error occurred while deactivating the division type. Please try again.',
            'warning_meta' => 'Business error while deactivating division type (UUID: :uuid).',
        ],

        'destroy' => [
            'success' => 'Division type has been deleted successfully.',
            'warning' => 'Attempt to delete a non-existent division type (UUID: :uuid).',
            'warning_meta' => 'Business error while deleting division type (UUID: :uuid).',
            'error' => 'Error while deleting division type (UUID: :uuid).',
            'flash_success' => 'Division type has been deleted successfully.',
            'flash_warning' => 'Division type not found.',
            'flash_error' => 'An error occurred while deleting the division type. Please try again.',
        ],

        'trashed' => [
            'success' => 'Deleted division types list retrieved successfully.',
            'error' => 'Error while retrieving the deleted division types list.',
        ],

        'restore' => [
            'success' => 'Division type has been restored successfully.',
            'warning' => 'Attempt to restore a non-existent division type (UUID: :uuid).',
            'warning_meta' => 'Business error while restoring division type (UUID: :uuid).',
            'not_found' => 'Division type not found.',
            'error' => 'Error while restoring division type (UUID: :uuid).',
            'flash_success' => 'Division type has been restored successfully.',
            'flash_warning' => 'Division type not found.',
            'flash_error' => 'An error occurred while restoring the division type. Please try again.',
        ],

        'force_destroy' => [
            'success' => 'Division type has been permanently deleted successfully.',
            'warning' => 'Attempt to permanently delete a non-existent division type (UUID: :uuid).',
            'error' => 'Error while permanently deleting division type (UUID: :uuid).',
            'not_found' => 'Division type not found.',
            'flash_success' => 'Division type has been permanently deleted successfully.',
            'flash_warning' => 'Division type not found.',
            'flash_error' => 'An error occurred while permanently deleting the division type. Please try again.',
            'warning_meta' => 'Business error while permanently deleting division type (UUID: :uuid).',
        ],

        'audits' => [
            'success' => 'Consultation of division type audits: :name',
            'error' => 'Error while consulting division type audits: :uuid',
        ],

        'activity-logs' => [
            'success' => 'Consultation of division type activity logs: :name',
            'error' => 'Error while consulting division type activity logs: :uuid',
        ],

        'translations' => [

            'not_found' => 'Attempt to access a non-existent division type translation (UUID: :uuid).',

            'destroy' => [
                'success' => 'Division type translation has been deleted successfully.',
                'warning' => 'Attempt to delete a non-existent division type translation (UUID: :uuid).',
                'warning_meta' => 'Business error while deleting division type translation (UUID: :uuid).',
                'error' => 'Error while deleting division type translation (UUID: :uuid).',
                'flash_success' => 'Division type translation has been deleted successfully.',
                'flash_warning' => 'Division type translation not found.',
                'flash_error' => 'An error occurred while deleting the division type translation. Please try again.',
            ],

            'trashed' => [
                'accessed' => 'Access to the division type translations trash ":name".',
                'not_found' => 'Division type not found. Impossible to access translations trash (UUID: :uuid).',
                'error' => 'An error occurred while accessing division type translations trash (UUID: :uuid). Please try again.',
            ],

            'restore' => [
                'success' => 'Division type translation has been restored successfully.',
                'warning' => 'Attempt to restore a non-existent division type translation (UUID: :uuid).',
                'warning_meta' => 'Business error while restoring division type translation (UUID: :uuid).',
                'not_found' => 'Division type translation not found.',
                'error' => 'Error while restoring division type translation (UUID: :uuid).',
                'flash_success' => 'Division type translation has been restored successfully.',
                'flash_warning' => 'Division type translation not found.',
                'flash_error' => 'An error occurred while restoring the division type translation. Please try again.',
            ],

            'force_destroy' => [
                'success' => 'Division type translation has been permanently deleted successfully.',
                'warning' => 'Attempt to permanently delete a non-existent division type translation (UUID: :uuid).',
                'error' => 'Error while permanently deleting division type translation (UUID: :uuid).',
                'not_found' => 'Division type translation not found.',
                'flash_success' => 'Division type translation has been permanently deleted successfully.',
                'flash_warning' => 'Division type translation not found.',
                'flash_error' => 'An error occurred while permanently deleting the division type translation. Please try again.',
                'warning_meta' => 'Business error while permanently deleting division type translation (UUID: :uuid).',
            ],
        ],

    ],

];
