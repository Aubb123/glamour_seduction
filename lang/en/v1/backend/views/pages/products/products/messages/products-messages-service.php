<?php

return [
    'products' => [
        'publish' => [
            'already_published' => 'This product is already published.',
        ],
        'unpublish' => [
            'not_published' => 'This product is not published (it cannot be unpublished).',
        ],
        'archive' => [
            'already_archived' => 'This product is already archived.',
        ],
        'destroy' => [
            'published_forbidden' => 'Cannot delete a published product. Please unpublish or archive it first.',
            'already_deleted' => 'This product is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only products already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only products already deleted or in the trash can be permanently deleted.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Cannot upload the image: the cloud platform was not selected.',
        ],
        'destroy_file' => [
            'success' => 'Image removed from product.',
            'not_found' => 'This image does not belong to this product.',
            'last_image_forbidden' => 'Cannot remove this image: the product must keep at least one image.',
            'warning_meta' => 'Business error while removing product image.',
            'error' => 'Error while removing product image.',
            'flash_success' => 'Image removed successfully.',
            'flash_error' => 'An error occurred while removing the image.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this product translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',
    ],

    'productsTranslations' => [
        'destroy' => [
            'product_not_found' => 'Cannot delete this translation: the product it is linked to cannot be found.',
            'translation_not_found' => 'The translation to delete cannot be found.',
            'last_translation_forbidden' => 'Cannot delete the last translation of the product.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'product_not_found' => 'Cannot permanently delete this translation: the product it is linked to cannot be found.',
            'translation_not_found' => 'The translation to permanently delete cannot be found.',
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the product.',
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be permanently deleted.',
        ],
    ],
];
