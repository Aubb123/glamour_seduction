<?php

return [
    'productBrands' => [
        'store' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the brand logo.',
            'cloud_platform_inactive' => 'The selected cloud platform is inactive.',
            'cloud_platform_not_supported' => 'The selected cloud platform is not supported for this upload.',
            'cloud_platform_folder_not_found' => 'No cloud folder was found for this entity and file type.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the brand logo.',
        ],
        'activate' => [
            'already_active' => 'This brand is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This brand is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active brand. Please deactivate it first.',
            'already_deleted' => 'This brand is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed brands can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed brands can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this brand translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',
    ],

    'productBrandsTranslations' => [
        'destroy' => [
            'last_translation_forbidden' => 'Cannot delete the last translation of the brand.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed translations can be restored.',
        ],
        'force_destroy' => [
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the brand.',
            'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
        ],
    ],
];
