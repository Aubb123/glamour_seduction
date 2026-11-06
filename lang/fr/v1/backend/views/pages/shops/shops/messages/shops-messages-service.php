<?php

return [
    'shops' => [
        'activate' => [
            'already_active' => 'Cette boutique est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette boutique est déjà inactive.',
        ],
        'update' => [
            'default_forbidden' => 'Impossible de retirer le statut « par défaut » à cette dernière boutique par défaut. Désignez d\'abord une autre boutique par défaut.',
        ],
        'warehouse_required' => 'Une boutique doit être rattachée à au moins un entrepôt.',
        'warehouse_not_found' => 'Un ou plusieurs entrepôts sélectionnés sont introuvables.',
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une boutique active. Veuillez la désactiver d\'abord.',
            'already_deleted' => 'Cette boutique est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les boutiques déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les boutiques déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction de la boutique (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',
    ],
    'shopsTranslations' => [
        'destroy' => [
            'shop_not_found' => 'Impossible de supprimer cette traduction : la boutique à laquelle elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer est introuvable.',
            'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction de la boutique.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'shop_not_found' => 'Impossible de supprimer définitivement cette traduction : la boutique à laquelle elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
            'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction de la boutique.',
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
