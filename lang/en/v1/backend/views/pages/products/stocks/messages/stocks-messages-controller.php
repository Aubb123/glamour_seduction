<?php

return [
    'productWarehouseStocks' => [
        'not_found' => 'Attempt to access a missing stock (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the stock list',
            'error' => 'Error while displaying the stock list',
        ],
        'create' => [
            'success' => 'Access to the stock creation page',
            'error' => 'Error while accessing the stock creation page',
        ],
        'store' => [
            'success' => 'Creating a new stock',
            'error' => 'Error while creating the stock',
            'flash_success' => 'Stock created successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'show' => [
            'success' => 'Viewing stock details',
            'warning' => 'Attempt to view a missing stock : :uuid',
            'error' => 'Error while viewing the stock : :uuid',
        ],
        'audits' => [
            'success' => 'Viewing stock audits',
            'warning' => 'Attempt to view audits of a missing stock : :uuid',
            'error' => 'Error while viewing stock audits : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing stock activity logs',
            'warning' => 'Attempt to view activity logs of a missing stock : :uuid',
            'error' => 'Error while viewing stock activity logs : :uuid',
        ],
        'edit' => [
            'success' => 'Access to the stock edit page',
            'warning' => 'Attempt to edit a missing stock : :uuid',
            'error' => 'Error while accessing the stock edit page : :uuid',
        ],
        'update' => [
            'success' => 'Updating the stock in database',
            'warning' => 'Attempt to update a missing stock : :uuid',
            'warning_meta' => 'Business error while updating the stock : :uuid',
            'error' => 'Error while updating the stock : :uuid',
            'flash_success' => 'Stock updated successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Deleting the stock',
            'warning' => 'Attempt to delete a missing stock : :uuid',
            'warning_meta' => 'Business error while deleting the stock : :uuid',
            'error' => 'Error while deleting the stock : :uuid',
            'flash_success' => 'Stock deleted successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
            'unknown' => 'Unknown stock',
        ],
        'trashed' => [
            'success' => 'Displaying the trashed stock list',
            'error' => 'Error while displaying the trashed stock list',
        ],
        'restore' => [
            'success' => 'Restoring the stock',
            'warning' => 'Attempt to restore a missing stock : :uuid',
            'warning_meta' => 'Business error while restoring the stock : :uuid',
            'error' => 'Error while restoring the stock : :uuid',
            'flash_success' => 'Stock restored successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting the stock',
            'warning' => 'Attempt to permanently delete a missing stock : :uuid',
            'warning_meta' => 'Business error while permanently deleting the stock : :uuid',
            'error' => 'Error while permanently deleting the stock : :uuid',
            'flash_success' => 'Stock permanently deleted successfully.',
            'flash_warning' => 'Stock not found.',
            'flash_error' => 'An error occurred.',
        ],
    ],
];
