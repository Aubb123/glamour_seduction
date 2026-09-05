<?php

return [

    // ─────────────────────────────────────────────
    // TRANSLATIONS
    // ─────────────────────────────────────────────

    'translations' => [
        'destroy' => [
            'last_translation_forbidden' => 'This translation cannot be deleted because it is the last remaining translation for this continent.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed translations can be restored.',
        ],
        'force_destroy' => [
            'last_translation_forbidden' => 'This translation cannot be permanently deleted because it is the last remaining translation for this continent.',
            'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
        ],
    ],

];
