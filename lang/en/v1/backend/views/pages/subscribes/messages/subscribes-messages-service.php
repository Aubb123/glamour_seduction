<?php

return [
    'subscribes' => [
        'destroy' => [
            'flash_warning' => 'You cannot delete an active subscription.',
            'already_deleted' => 'This entity is already deleted and cannot be deleted again.',
        ],
        'force_destroy' => [
            'flash_warning' => 'You cannot permanently delete an active subscription.',
            'not_trashed_forbidden' => 'Only trashed subscriptions can be permanently deleted.',
        ],

        // ────────────────────────────────────────────
        // SUBSCRIBE HISTORY
        // ────────────────────────────────────────────
        'histories' => [
            'destroy' => [
                'flash_warning' => 'The subscription history cannot be deleted, it is kept for traceability and audit purposes.',
                'already_deleted' => 'This entity is already deleted and cannot be deleted again.',
            ],
            'force_destroy' => [
                'flash_warning' => 'The subscription history cannot be permanently deleted, it is kept for traceability and audit purposes.',
                'not_trashed_forbidden' => 'Only trashed subscription histories can be permanently deleted.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed subscription histories can be restored.',
            ],
        ],

    ],
];
