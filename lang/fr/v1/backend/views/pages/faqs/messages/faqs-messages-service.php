<?php

return [
    'faqs' => [
        'destroy' => [
            'active_faq_forbidden' => 'Impossible de supprimer une FAQ active. Veuillez d\'abord la désactiver.',
            'already_deleted' => 'Cette FAQ est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les FAQs déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'active_faq_forbidden' => 'Impossible de supprimer définitivement une FAQ active. Veuillez d\'abord la désactiver.',
            'not_trashed_forbidden' => 'Seules les FAQs déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de la FAQ (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        'translations' => [
            'destroy' => [
                'faq_not_found' => 'Impossible de supprimer cette traduction : la FAQ à laquelle elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de cette FAQ.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
            ],
            'force_destroy' => [
                'faq_not_found' => 'Impossible de supprimer définitivement cette traduction : la FAQ à laquelle elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de cette FAQ.',
                'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
            ],
        ],
    ],
];
