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
        ],
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
