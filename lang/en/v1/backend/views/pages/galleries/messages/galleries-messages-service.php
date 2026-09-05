<?php

return [
    'galleries' => [
        'destroy' => [
            'active_gallery_forbidden' => 'The gallery (:name) is active and cannot be deleted.',
            'already_deleted' => 'This gallery is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed galleries can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_gallery_forbidden' => 'The gallery (:name) is not trashed and cannot be permanently deleted.',
            'active_gallery_forbidden' => 'The gallery (:name) is active and cannot be permanently deleted.',
            'not_trashed_forbidden' => 'Only trashed galleries can be permanently deleted.',
        ],
        'store' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the gallery image.',
            'cloud_platform_inactive' => 'The selected cloud platform is inactive.',
            'cloud_platform_not_supported' => 'The selected cloud platform is not supported for this upload.',
            'cloud_platform_folder_not_found' => 'No cloud folder was found for this entity and file type.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the gallery image.',
        ],

        // ────────────────────────────────────────────
        // TRANSLATIONS
        // ────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'The translation (:name) is the last remaining translation for this gallery and cannot be deleted.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'The translation (:name) is the last remaining translation for this gallery and cannot be permanently deleted.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
