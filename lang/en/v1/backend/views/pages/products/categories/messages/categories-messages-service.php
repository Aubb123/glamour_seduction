<?php

return [
    'productCategories' => [
        'store' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the category image.',
            'cloud_platform_inactive' => 'The selected cloud platform is inactive.',
            'cloud_platform_not_supported' => 'The selected cloud platform is not supported for this upload.',
            'cloud_platform_folder_not_found' => 'No cloud folder was found for this entity and file type.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the category image.',
            'parent_self_forbidden' => 'A category cannot be its own parent category.',
            'parent_descendant_forbidden' => 'Cannot attach this category to one of its own sub-categories: this would create a cycle in the tree structure.',
        ],
        'activate' => [
            'already_active' => 'This category is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This category is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active category. Please deactivate it first.',
            'already_deleted' => 'This category is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed categories can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed categories can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this product category translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',
    ],

    'productCategoriesTranslations' => [
        'destroy' => [
            'category_not_found' => 'Cannot delete this translation: the category it is linked to cannot be found.',
            'translation_not_found' => 'The translation to delete cannot be found.',
            'last_translation_forbidden' => 'Cannot delete the last translation of the category.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed translations can be restored.',
        ],
        'force_destroy' => [
            'category_not_found' => 'Cannot permanently delete this translation: the category it is linked to cannot be found.',
            'translation_not_found' => 'The translation to permanently delete cannot be found.',
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the category.',
            'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
        ],
    ],
];
