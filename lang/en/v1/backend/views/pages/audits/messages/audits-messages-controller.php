<?php

return [
    'audits' => [
        'not_found' => 'Attempt to access a non-existent audit (UUID: :uuid).',
        'index' => [
            'success' => 'Viewing the audits list',
            'error' => 'Error viewing the audits list',
        ],
        'show' => [
            'success' => 'Viewing audit details',
            'warning' => 'Attempt to view a non-existent audit: :uuid',
            'error' => 'Error viewing audit: :uuid',
        ],
        'destroy' => [
            'success' => 'Deleting audit from database',
            'warning' => 'Attempt to delete a non-existent audit: :uuid',
            'error' => 'Error deleting audit: :uuid',
            'flash_success' => 'Audit deleted successfully.',
            'flash_warning' => 'Audit not found.',
            'flash_error' => 'An error occurred.',
            'flash_info' => 'Unable to delete the audit. Please contact the developer.',
        ],
        'clear' => [
            'success' => 'Clearing old audits: :count deleted',
            'error' => 'Error clearing old audits',
            'flash_success' => ':count old audit(s) deleted successfully.',
            'flash_error' => 'An error occurred while clearing audits.',
            'flash_info' => 'Unable to clear the audits. Please contact the developer.',
        ],
    ],
];
