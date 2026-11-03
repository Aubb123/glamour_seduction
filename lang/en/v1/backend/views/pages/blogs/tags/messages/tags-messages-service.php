<?php

return [
    'tags' => [
        'activate' => [
            'already_active' => 'This tag is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This tag is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active tag. Please deactivate it first.',
            'already_deleted' => 'This tag is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed tags can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed tags can be permanently deleted.',
        ],
        // --─────────────────────────────────────────────
        // TRANSLATIONS
        // --─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'tag_not_found' => 'Cannot delete this translation: the tag it is linked to cannot be found.',
                'translation_not_found' => 'The translation to delete cannot be found.',
                'last_translation_forbidden' => 'Cannot delete the last translation of the tag.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'tag_not_found' => 'Cannot permanently delete this translation: the tag it is linked to cannot be found.',
                'translation_not_found' => 'The translation to permanently delete cannot be found.',
                'last_translation_forbidden' => 'Cannot permanently delete the last translation of the tag.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
