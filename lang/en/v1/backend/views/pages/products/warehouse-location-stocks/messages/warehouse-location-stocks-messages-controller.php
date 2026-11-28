<?php

return [
    'warehouseLocationStocks' => [
        'not_found' => 'Attempt to access a missing location stock (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the location stocks list',
            'error' => 'Error while displaying the location stocks list',
        ],
        'create' => [
            'success' => 'Accessing the location stock creation page',
            'error' => 'Error while accessing the location stock creation page',
        ],
        'store' => [
            'success' => 'Creating a new location stock: :label',
            'error' => 'Error while creating a location stock',
            'flash_success' => 'Location stock (:label) created successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'show' => [
            'success' => 'Viewing location stock details: :label',
            'error' => 'Error while viewing location stock: :uuid',
        ],
        'audits' => [
            'success' => 'Viewing location stock audits: :label',
            'error' => 'Error while viewing location stock audits: :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing location stock activity logs: :label',
            'error' => 'Error while viewing location stock activity logs: :uuid',
        ],
        'edit' => [
            'success' => 'Accessing the location stock edit page: :label',
            'error' => 'Error while accessing the location stock edit page: :uuid',
        ],
        'update' => [
            'success' => 'Updating location stock in database: :label',
            'warning_meta' => 'Business error while updating location stock: :uuid',
            'error' => 'Error while updating location stock: :uuid',
            'flash_success' => 'Location stock (:label) updated successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Deleting location stock: :label',
            'warning_meta' => 'Business error while deleting location stock: :uuid',
            'error' => 'Error while deleting location stock: :uuid',
            'flash_success' => 'Location stock (:label) deleted successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Displaying trashed location stocks',
            'error' => 'Error while displaying trashed location stocks',
        ],
        'restore' => [
            'success' => 'Restoring location stock: :label',
            'flash_success' => 'Location stock (:label) restored successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
            'error' => 'Error while restoring location stock: :uuid',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting location stock: :label',
            'flash_success' => 'Location stock (:label) permanently deleted successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
            'error' => 'Error while permanently deleting location stock: :uuid',
        ],
    ],
];
