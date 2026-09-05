<?php

return [
    'activity_logs' => [
        'not_found' => 'Attempt to access a non-existent activity log (UUID: :uuid).',
        'index' => [
            'success' => 'Viewing the activity logs list',
            'error' => 'Error viewing the activity logs list',
        ],
        'show' => [
            'success' => 'Viewing activity log details',
            'warning' => 'Attempt to view a non-existent activity log: :uuid',
            'error' => 'Error viewing activity log: :uuid',
        ],
        'destroy' => [
            'flash_info' => 'Unable to delete the activity log. Please contact the developer for more information.',
            'success' => 'Deleting activity log from database',
            'warning' => 'Attempt to delete a non-existent activity log: :uuid',
            'error' => 'Error deleting activity log: :uuid',
            'flash_success' => 'Activity log deleted successfully.',
            'flash_warning' => 'Activity log not found.',
            'flash_error' => 'An error occurred.',
        ],
        'clear' => [
            'flash_info' => 'Unable to clear activity logs. Please contact the developer for more information.',
            'success' => 'Clearing old activity logs: :count deleted',
            'error' => 'Error clearing old activity logs',
            'flash_success' => ':count old activity log(s) deleted successfully.',
            'flash_error' => 'An error occurred while clearing activity logs.',
        ],
    ],
];
