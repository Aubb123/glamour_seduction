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
                'role_not_found' => 'Cannot delete this translation: the role it is linked to cannot be found.',
                'translation_not_found' => 'The translation to delete cannot be found.',
                'last_translation_forbidden' => 'Cannot delete this translation because it is the last remaining translation for this role.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'role_not_found' => 'Cannot permanently delete this translation: the role it is linked to cannot be found.',
                'translation_not_found' => 'The translation to permanently delete cannot be found.',
                'last_translation_forbidden' => 'Cannot permanently delete this translation because it is the last remaining translation for this role.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
