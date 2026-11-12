<?php

return [
    'products' => [
        'publish' => [
            'already_published' => 'Ce produit est déjà publié.',
        ],
        'unpublish' => [
            'not_published' => 'Ce produit n\'est pas publié (il ne peut pas être dépublié).',
        ],
        'archive' => [
            'already_archived' => 'Ce produit est déjà archivé.',
        ],
        'destroy' => [
            'published_forbidden' => 'Impossible de supprimer un produit publié. Veuillez le dépublier ou l\'archiver d\'abord.',
            'already_deleted' => 'Ce produit est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les produits déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les produits déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
            'active_forbidden' => 'Impossible de supprimer définitivement un produit publié.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Impossible de téléverser l\'image : la plateforme cloud n\'a pas été sélectionnée.',
            'image_limit_exceeded' => 'Un produit ne peut pas comporter plus de 4 images au total.',
        ],
        'destroy_file' => [
            'not_found' => 'Cette image n\'appartient pas à ce produit.',
            'last_image_forbidden' => 'Impossible de retirer cette image : le produit doit conserver au moins une image.',
        ],
        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction du produit (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',
    ],
    'productsTranslations' => [
        'destroy' => [
            'product_not_found' => 'Impossible de supprimer cette traduction : le produit auquel elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer est introuvable.',
            'last_translation_forbidden' => 'Impossible de supprimer la dernière traduction du produit.',
            'already_deleted' => 'Cette traduction est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'product_not_found' => 'Impossible de supprimer définitivement cette traduction : le produit auquel elle est liée est introuvable.',
            'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
            'last_translation_forbidden' => 'Impossible de supprimer définitivement la dernière traduction du produit.',
            'not_trashed_forbidden' => 'Seules les traductions déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
