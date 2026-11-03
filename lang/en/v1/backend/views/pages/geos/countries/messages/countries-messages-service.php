<?php

return [

    'countries' => [

        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'already_active' => 'This country is already active.',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'already_inactive' => 'This country is already inactive.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this country translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────

        // ─────────────────────────────────────────────
        // TRANSLATIONS DESTROY
        // ─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'country_not_found' => 'Cannot delete this translation: the country it is linked to cannot be found.',
                'translation_not_found' => 'The translation to delete cannot be found.',
                'last_translation_forbidden' => 'Cannot delete this translation because it is the last remaining translation for this country.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'country_not_found' => 'Cannot permanently delete this translation: the country it is linked to cannot be found.',
                'translation_not_found' => 'The translation to permanently delete cannot be found.',
                'last_translation_forbidden' => 'Cannot permanently delete this translation because it is the last remaining translation for this country.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],

    ],

];
