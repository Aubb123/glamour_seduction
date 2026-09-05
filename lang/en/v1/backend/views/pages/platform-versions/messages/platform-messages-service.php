<?php

return [
    'versions' => [
        'update' => [
            'unpublish_forbidden' => 'A published version cannot be unpublished. Please contact the developer if you wish to unpublish this version.',
        ],
        'destroy' => [
            'published_forbidden' => 'A published version cannot be deleted. Please contact the developer if you wish to delete this version.',
            'active_forbidden' => 'An active version cannot be deleted. Please deactivate this version first before deleting it.',
            'already_deleted' => 'This version is already deleted and cannot be deleted again.',
        ],
        'publish' => [
            'already_published' => 'This version is already published. Please select another version to publish.',
        ],
        'unpublish' => [
            'published_forbidden' => 'A published version cannot be unpublished. Please contact the developer if you wish to unpublish this version.',
            'already_unpublished' => 'This version is already unpublished. Please select another version to unpublish.',
        ],
        'activate' => [
            'already_active' => 'This version is already active. Please select another version to activate.',
            'published_required' => 'Only published versions can be activated. Please publish this version before activating it.',
        ],
        'deactivate' => [
            'already_inactive' => 'This version is already inactive. Please select another version to deactivate.',
        ],

        'restore' => [
            'not_trashed_forbidden' => 'Only trashed versions can be restored.',
            'code_conflict' => 'Cannot restore this version: its code is already taken by another active version.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed versions can be permanently deleted.',
        ],

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'This translation cannot be deleted because it is the last remaining translation for this version.',
                'version_not_found' => 'The version associated with this translation was not found. Please ensure the version exists and try again.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
                'lang_conflict' => 'Cannot restore this translation: another active translation already exists for this language.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'This translation cannot be permanently deleted because it is the last remaining translation for this version.',
                'version_not_found' => 'The version associated with this translation was not found. Please ensure the version exists and try again.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],

    ],
];
