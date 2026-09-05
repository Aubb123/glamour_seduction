<?php

return [

    // ─────────────────────────────────────────────
    // TRANSLATIONS
    // ─────────────────────────────────────────────

    'translations' => [
        'destroy' => [
            'last_translation_forbidden' => 'Cette traduction ne peut pas être supprimée car c\'est la dernière traduction restante pour ce continent.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut pas être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être restaurées.',
        ],
        'force_destroy' => [
            'last_translation_forbidden' => 'Cette traduction ne peut pas être supprimée définitivement car c\'est la dernière traduction restante pour ce continent.',
            'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être supprimées définitivement.',
        ],
    ],

];
