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

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this tag translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        // --─────────────────────────────────────────────
        // TRANSLATIONS
        // --─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Cannot delete the last translation of the tag.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Cannot permanently delete the last translation of the tag.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
