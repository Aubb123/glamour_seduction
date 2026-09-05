<?php

return [
    'roles' => [
        'activate' => [
            'already_active' => 'This role is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This role is already inactive.',
        ],
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Cannot delete this translation because it is the last remaining translation for this role.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Cannot permanently delete this translation because it is the last remaining translation for this role.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
