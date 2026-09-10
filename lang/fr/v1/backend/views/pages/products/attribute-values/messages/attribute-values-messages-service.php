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
    ],

    'productAttributeValuesTranslations' => [
        'destroy' => [
            'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de la valeur d\'attribut.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de la valeur d\'attribut.',
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];