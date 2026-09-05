<?php

return [
    'replies' => [
        'not_found' => 'Attempt to access a non-existent blog comment reply (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the comment replies list',
            'error' => 'Error while displaying the comment replies list',
        ],
        'show' => [
            'success' => 'Viewing reply details: :content',
            'error' => 'Error while viewing reply: :uuid',
        ],
        'activate' => [
            'success' => 'Reply approved: :content',
            'error' => 'Error while approving reply: :uuid',
            'flash_success' => 'Reply (:content) approved successfully.',
            'flash_error' => 'An error occurred.',
            'warning_meta' => 'Business error while approving reply: :uuid',
        ],
        'deactivate' => [
            'success' => 'Reply disapproved: :content',
            'error' => 'Error while disapproving reply: :uuid',
            'flash_success' => 'Reply (:content) disapproved successfully.',
            'flash_error' => 'An error occurred.',
            'warning_meta' => 'Business error while disapproving reply: :uuid',
        ],
        'destroy' => [
            'success' => 'Reply deleted from database: :content',
            'warning_meta' => 'Business error while deleting reply: :uuid',
            'error' => 'Error while deleting reply: :uuid',
            'flash_success' => 'Reply (:content) deleted successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Displaying the trashed replies list',
            'error' => 'Error while displaying the trashed replies list',
        ],
        'restore' => [
            'success' => 'Reply restored: :content',
            'warning' => 'Attempt to restore a non-existent reply: :uuid',
            'warning_meta' => 'Business error while restoring reply: :uuid',
            'error' => 'Error while restoring reply: :uuid',
            'flash_success' => 'Reply (:content) restored successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Reply permanently deleted: :uuid',
            'warning' => 'Attempt to permanently delete a non-existent reply: :uuid',
            'warning_meta' => 'Business error while permanently deleting reply: :uuid',
            'error' => 'Error while permanently deleting reply: :uuid',
            'flash_success' => 'Reply permanently deleted successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'audits' => [
            'success' => 'Viewing reply audits: :content',
            'error' => 'Error while viewing reply audits: :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing reply activity logs: :content',
            'error' => 'Error while viewing reply activity logs: :uuid',
        ],
    ],
];
