<?php

return [
    'comments' => [
        'activate' => [
            'already_approved' => 'This comment is already approved.',
        ],
        'deactivate' => [
            'already_disapproved' => 'This comment is already disapproved.',
        ],
        'destroy' => [
            'visible_forbidden' => 'Cannot delete an approved comment. Please disapprove it first.',
            'already_deleted' => 'This comment is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed comments can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed comments can be permanently deleted.',
        ],
    ],
];
