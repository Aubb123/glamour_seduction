<?php

return [
    'contacts' => [
        'destroy' => [
            'flash_warning' => 'Only read messages can be deleted.',
            'already_deleted' => 'This message is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed messages can be restored.',
        ],
        'force_destroy' => [
            'flash_warning' => 'Only read messages can be permanently deleted.',
            'not_trashed_forbidden' => 'Only trashed messages can be permanently deleted.',
        ],
    ],

];
