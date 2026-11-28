<?php

return [
    'warehouseStocks' => [
        'not_found' => 'Attempt to access a missing stock (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the stocks list',
            'error' => 'Error while displaying the stocks list',
        ],
        'create' => [
            'success' => 'Accessing the stock creation page',
            'error' => 'Error while accessing the stock creation page',
        ],
        'store' => [
            'success' => 'Creating a new stock: :label',
            'error' => 'Error while creating a stock',
            'flash_success' => 'Stock (:label) created successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'show' => [
            'success' => 'Viewing stock details: :label',
            'error' => 'Error while viewing stock: :uuid',
        ],
        'audits' => [
            'success' => 'Viewing stock audits: :label',
            'error' => 'Error while viewing stock audits: :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing stock activity logs: :label',
            'error' => 'Error while viewing stock activity logs: :uuid',
        ],
        'edit' => [
            'success' => 'Accessing the stock edit page: :label',
            'error' => 'Error while accessing the stock edit page: :uuid',
        ],
        'update' => [
            'success' => 'Updating stock in database: :label',
            'warning_meta' => 'Business error while updating stock: :uuid',
            'error' => 'Error while updating stock: :uuid',
            'flash_success' => 'Stock (:label) updated successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Deleting stock: :label',
            'warning_meta' => 'Business error while deleting stock: :uuid',
            'error' => 'Error while deleting stock: :uuid',
            'flash_success' => 'Stock (:label) deleted successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Displaying trashed stocks',
            'error' => 'Error while displaying trashed stocks',
        ],
        'restore' => [
            'success' => 'Restoring stock: :label',
            'flash_success' => 'Stock (:label) restored successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
            'error' => 'Error while restoring stock: :uuid',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting stock: :label',
            'flash_success' => 'Stock (:label) permanently deleted successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
            'error' => 'Error while permanently deleting stock: :uuid',
        ],
    ],
];
