<?php

return [
    'productAttributeValues' => [
        'activate' => [
            'already_active' => 'Cette valeur d\'attribut est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette valeur d\'attribut est déjà inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une valeur d\'attribut active. Veuillez la désactiver d\'abord.',
            'already_deleted' => 'Cette valeur d\'attribut est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les valeurs d\'attributs déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les valeurs d\'attributs déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],

        // Refus de mise à jour d'une traduction se trouvant en corbeille (garde syncTranslations)
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de la valeur d\'attribut (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',
    ],

    'productAttributeValuesTranslations' => [
        'destroy' => [
            'attribute_value_not_found' => 'Impossible de supprimer cette traduction : la valeur d\'attribut à laquelle elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer est introuvable.',
            'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de la valeur d\'attribut.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'attribute_value_not_found' => 'Impossible de supprimer définitivement cette traduction : la valeur d\'attribut à laquelle elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
            'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de la valeur d\'attribut.',
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
