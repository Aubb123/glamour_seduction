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

    // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
    'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction du continent (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

];
