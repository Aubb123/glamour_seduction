<?php

return [
    'productAttributes' => [
        'activate' => [
            'already_active' => 'This attribute is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This attribute is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active attribute. Please deactivate it first.',
            'already_deleted' => 'This attribute is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only attributes already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only attributes already deleted or in the trash can be permanently deleted.',
        ],
    ],

    'productAttributesTranslations' => [
        'destroy' => [
            'last_translation_forbidden' => 'Cannot delete the last translation of the attribute.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the attribute.',
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be permanently deleted.',
        ],
    ],
];