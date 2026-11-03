<?php

return [

    // ─────────────────────────────────────────────
    // TRANSLATIONS
    // ─────────────────────────────────────────────

    'translations' => [
        'destroy' => [
            'continent_not_found' => 'Cannot delete this translation: the continent it is linked to cannot be found.',
            'translation_not_found' => 'The translation to delete cannot be found.',
            'last_translation_forbidden' => 'This translation cannot be deleted because it is the last remaining translation for this continent.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed translations can be restored.',
        ],
        'force_destroy' => [
            'continent_not_found' => 'Cannot permanently delete this translation: the continent it is linked to cannot be found.',
            'translation_not_found' => 'The translation to permanently delete cannot be found.',
            'last_translation_forbidden' => 'This translation cannot be permanently deleted because it is the last remaining translation for this continent.',
            'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this continent translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',
    ],

];
