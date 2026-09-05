<?php

return [
    'replies' => [
        'activate' => [
            'already_approved' => 'This reply is already approved.',
        ],
        'deactivate' => [
            'already_disapproved' => 'This reply is already disapproved.',
        ],
        'destroy' => [
            'visible_forbidden' => 'Cannot delete an approved reply. Please disapprove it first.',
            'already_deleted' => 'This reply is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed replies can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed replies can be permanently deleted.',
        ],
    ],
];
