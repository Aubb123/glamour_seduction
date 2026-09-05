<?php

return [
    'permissions' => [
        'activate' => [
            'already_active' => 'This permission is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This permission is already inactive.',
        ],
        'update' => [
            'cannot_rename_used' => 'Cannot rename this permission as it is used by roles or users.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active permission. Please deactivate it first.',
            'has_roles' => 'Cannot delete this permission as it is used by roles.',
            'has_users' => 'Cannot delete this permission as it is used by users.',
            'already_deleted' => 'This entity is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_deleted' => 'This permission is not deleted.',
            'not_trashed_forbidden' => 'Only trashed permissions can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed permissions can be permanently deleted.',
        ],
    ],
];
