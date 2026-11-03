<?php

return [

    // ─────────────────────────────────────────────
    // TRANSLATIONS
    // ─────────────────────────────────────────────

    'translations' => [
        'destroy' => [
            'continent_not_found' => 'Impossible de supprimer cette traduction : le continent auquel elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer est introuvable.',
            'last_translation_forbidden' => 'Cette traduction ne peut pas être supprimée car c\'est la dernière traduction restante pour ce continent.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut pas être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être restaurées.',
        ],
        'force_destroy' => [
            'continent_not_found' => 'Impossible de supprimer définitivement cette traduction : le continent auquel elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
            'last_translation_forbidden' => 'Cette traduction ne peut pas être supprimée définitivement car c\'est la dernière traduction restante pour ce continent.',
            'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être supprimées définitivement.',
        ],
    ],

];
