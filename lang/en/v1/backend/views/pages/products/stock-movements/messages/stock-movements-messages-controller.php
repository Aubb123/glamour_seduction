<?php

return [
    'productStockMovements' => [
        'not_found' => 'Attempt to access a missing stock movement (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the stock movements list',
            'error' => 'Error while displaying the stock movements list',
        ],
        'create' => [
            'success' => 'Access to the stock movement creation page',
            'error' => 'Error while accessing the stock movement creation page',
        ],
        'store' => [
            'success' => 'Recording a new stock movement',
            'error' => 'Error while recording the stock movement',
            'flash_success' => 'Stock movement recorded successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'show' => [
            'success' => 'Viewing stock movement details',
            'warning' => 'Attempt to view a missing movement : :uuid',
            'error' => 'Error while viewing the stock movement : :uuid',
        ],
        'audits' => [
            'success' => 'Viewing audits of the stock movement',
            'warning' => 'Attempt to view audits of a missing movement : :uuid',
            'error' => 'Error while viewing audits of the stock movement : :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing activity logs of the stock movement',
            'warning' => 'Attempt to view activity logs of a missing movement : :uuid',
            'error' => 'Error while viewing activity logs of the stock movement : :uuid',
        ],
        'destroy' => [
            'success' => 'Deleting the stock movement',
            'warning' => 'Attempt to delete a missing movement : :uuid',
            'warning_meta' => 'Business error while deleting the movement : :uuid',
            'error' => 'Error while deleting the movement : :uuid',
            'flash_success' => 'Movement deleted successfully.',
            'flash_warning' => 'Movement not found.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Displaying the trashed stock movements list',
            'error' => 'Error while displaying the trashed movements list',
        ],
        'restore' => [
            'success' => 'Restoring the stock movement',
            'warning' => 'Attempt to restore a missing movement : :uuid',
            'warning_meta' => 'Business error while restoring the movement : :uuid',
            'error' => 'Error while restoring the movement : :uuid',
            'flash_success' => 'Stock movement restored successfully.',
            'flash_warning' => 'Movement not found.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting the stock movement',
            'warning' => 'Attempt to permanently delete a missing movement : :uuid',
            'warning_meta' => 'Business error while permanently deleting the movement : :uuid',
            'error' => 'Error while permanently deleting the movement : :uuid',
            'flash_success' => 'Movement permanently deleted successfully.',
            'flash_warning' => 'Movement not found.',
            'flash_error' => 'An error occurred.',
        ],
    ],
];
