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
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Cannot delete the last translation of this FAQ.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Cannot permanently delete the last translation of this FAQ.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
