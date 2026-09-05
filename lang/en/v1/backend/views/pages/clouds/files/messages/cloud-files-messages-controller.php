<?php

return [
    'cloud-files' => [
        'not_found' => 'Attempt to access a non-existent cloud file (UUID: :uuid).',
        'index' => [
            'success' => 'Viewing the cloud files list',
            'error' => 'Error viewing the cloud files list',
        ],
        'show' => [
            'success' => 'Viewing cloud file details: :name',
            'warning' => 'Attempt to view a non-existent cloud file: :uuid',
            'error' => 'Error viewing cloud file: :uuid',
            'not_found' => 'Cloud file with UUID :uuid was not found.',
            'proxy_error' => 'Cloud file proxy error: :message',
        ],
        'audits' => [
            'success' => 'Viewing the audits of cloud file: :name',
            'error' => 'Error viewing the audits of cloud file: :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing the activity logs of cloud file: :name',
            'error' => 'Error viewing the activity logs of cloud file: :uuid',
        ],
        'destroy' => [
            'success' => 'Deleting cloud file: :name',
            'flash_success' => 'Cloud file (:name) deleted temporarily with success.',
            'warning' => 'Attempt to delete a non-existent cloud file: :uuid',
            'flash_warning' => 'Cloud file not found.',
            'invalid_argument' => 'Invalid argument provided for deleting cloud file: :uuid',
            'error' => 'Error deleting cloud file: :uuid',
            'flash_error' => 'An error occurred while deleting the cloud file.',
        ],
        'sync' => [
            'success' => 'Viewing the cloud files synchronization page',
            'error' => 'Error viewing the cloud files synchronization page',
            'no_selection' => 'Please select at least one file to synchronize.',
            'flash_success' => ':success file(s) synchronized successfully, :failed failed.',
            'flash_invalid_argument' => 'Invalid argument provided for cloud files synchronization.',
            'flash_error' => 'An error occurred while synchronizing the cloud files.',
        ],
        'trashed' => [
            'success' => 'Viewing the trashed cloud files list',
            'error' => 'Error viewing the trashed cloud files list',
        ],
        'restore' => [
            'success' => 'Restoring cloud file: :name',
            'flash_success' => 'Cloud file (:name) restored successfully.',
            'warning' => 'Attempt to restore a non-existent cloud file: :uuid',
            'warning_meta' => 'Attempt to restore a cloud file with invalid argument: :uuid',
            'error' => 'Error restoring cloud file: :uuid',
            'flash_error' => 'An error occurred while restoring the cloud file.',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting cloud file: :name',
            'flash_success' => 'Cloud file (:name) permanently deleted successfully.',
            'warning' => 'Attempt to permanently delete a non-existent cloud file: :uuid',
            'warning_meta' => 'Attempt to permanently delete a cloud file with invalid argument: :uuid',
            'error' => 'Error permanently deleting cloud file: :uuid',
            'flash_error' => 'An error occurred while permanently deleting the cloud file.',
        ],
    ],
];
