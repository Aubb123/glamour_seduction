<?php

return [
    'faqs' => [
        'destroy' => [
            'active_faq_forbidden' => 'Cannot delete an active FAQ.',
            'already_deleted' => 'This FAQ is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed FAQs can be restored.',
        ],
        'force_destroy' => [
            'active_faq_forbidden' => 'Cannot permanently delete an active FAQ.',
            'not_trashed_forbidden' => 'Only trashed FAQs can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this FAQ translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        'translations' => [
            'destroy' => [
                'faq_not_found' => 'Cannot delete this translation: the FAQ it is linked to cannot be found.',
                'translation_not_found' => 'The translation to delete cannot be found.',
                'last_translation_forbidden' => 'Cannot delete the last translation of this FAQ.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'faq_not_found' => 'Cannot permanently delete this translation: the FAQ it is linked to cannot be found.',
                'translation_not_found' => 'The translation to permanently delete cannot be found.',
                'last_translation_forbidden' => 'Cannot permanently delete the last translation of this FAQ.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
