<?php

return [
    'warehouseLocations' => [
        'activate' => [
            'already_active' => 'This location is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This location is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'An active location cannot be deleted. Please deactivate it first.',
            'already_deleted' => 'This location is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only deleted or trashed locations can be restored.',
            'parent_trashed' => 'This location cannot be restored: its parent location is in the trash.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only deleted or trashed locations can be permanently deleted.',
            'active_forbidden' => 'An active location cannot be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'This location translation cannot be updated (language: :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        // ─────────────────────────────────────────────
        // Translations
        // ─────────────────────────────────────────────
        'translations' => [
            'name' => [
                'unique_in_warehouse' => 'The name ":name" is already used by another location of this warehouse for this language (trash included).',
            ],
            'destroy' => [
                'location_not_found' => 'This translation cannot be deleted: its parent location cannot be found.',
                'translation_not_found' => 'The translation to delete cannot be found.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
                'last_translation_forbidden' => 'The last translation of the location cannot be deleted.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only deleted or trashed translations can be restored.',
                'location_not_found' => 'This translation cannot be restored: its parent location cannot be found.',
                'name_conflict' => 'This translation cannot be restored: its name is already used by another location of this warehouse for this language.',
            ],
            'force_destroy' => [
                'location_not_found' => 'This translation cannot be permanently deleted: its parent location cannot be found.',
                'translation_not_found' => 'The translation to permanently delete cannot be found.',
                'not_trashed_forbidden' => 'Only deleted or trashed translations can be permanently deleted.',
                'last_translation_forbidden' => 'The last translation of the location cannot be permanently deleted.',
            ],
        ],
    ],
];
