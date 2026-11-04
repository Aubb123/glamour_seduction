<?php

return [
    'productVariants' => [
        'activate' => [
            'already_active' => 'This variant is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This variant is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active variant. Please deactivate it first.',
            'already_deleted' => 'This variant is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only variants already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only variants already deleted or in the trash can be permanently deleted.',
        ],
    ],
];
