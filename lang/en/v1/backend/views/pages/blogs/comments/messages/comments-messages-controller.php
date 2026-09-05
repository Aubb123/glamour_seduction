<?php

return [
    'comments' => [
        'not_found' => 'Attempt to access a non-existent blog comment (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the comments list',
            'error' => 'Error while displaying the comments list',
        ],
        'show' => [
            'success' => 'Viewing comment details: :content',
            'error' => 'Error while viewing comment: :uuid',
        ],
        'activate' => [
            'success' => 'Comment approved: :content',
            'error' => 'Error while approving comment: :uuid',
            'flash_success' => 'Comment (:content) approved successfully.',
            'flash_error' => 'An error occurred.',
            'warning_meta' => 'Business error while approving comment: :uuid',
        ],
        'deactivate' => [
            'success' => 'Comment disapproved: :content',
            'error' => 'Error while disapproving comment: :uuid',
            'flash_success' => 'Comment (:content) disapproved successfully.',
            'flash_error' => 'An error occurred.',
            'warning_meta' => 'Business error while disapproving comment: :uuid',
        ],
        'destroy' => [
            'success' => 'Comment deleted from database: :content',
            'warning_meta' => 'Business error while deleting comment: :uuid',
            'error' => 'Error while deleting comment: :uuid',
            'flash_success' => 'Comment (:content) deleted successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Displaying the trashed comments list',
            'error' => 'Error while displaying the trashed comments list',
        ],
        'restore' => [
            'success' => 'Comment restored: :content',
            'warning' => 'Attempt to restore a non-existent comment: :uuid',
            'warning_meta' => 'Business error while restoring comment: :uuid',
            'error' => 'Error while restoring comment: :uuid',
            'flash_success' => 'Comment (:content) restored successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Comment permanently deleted: :uuid',
            'warning' => 'Attempt to permanently delete a non-existent comment: :uuid',
            'warning_meta' => 'Business error while permanently deleting comment: :uuid',
            'error' => 'Error while permanently deleting comment: :uuid',
            'flash_success' => 'Comment permanently deleted successfully.',
            'flash_error' => 'An error occurred.',
        ],
        'audits' => [
            'success' => 'Viewing comment audits: :content',
            'error' => 'Error while viewing comment audits: :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing comment activity logs: :content',
            'error' => 'Error while viewing comment activity logs: :uuid',
        ],
        'replies' => [
            'success' => 'Viewing comment replies: :content',
            'error' => 'Error while viewing comment replies: :uuid',
        ],
    ],
];
