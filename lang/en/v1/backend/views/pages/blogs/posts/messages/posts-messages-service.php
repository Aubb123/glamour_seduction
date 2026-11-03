<?php

return [
    'posts' => [
        'scheduled_date_required' => 'A publication date is required to schedule a post.',
        'scheduled_date_past' => 'The scheduled publication date must be in the future.',

        'store' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the post image.',
            'cloud_platform_inactive' => 'The selected cloud platform is inactive.',
            'cloud_platform_not_supported' => 'The selected cloud platform is not supported for this upload.',
            'cloud_platform_folder_not_found' => 'No cloud folder was found for this entity and file type.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload the post image.',
        ],
        'destroy' => [
            'published_forbidden' => 'Cannot delete a published post. Please set it to draft first.',
            'already_deleted' => 'This post is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed posts can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only trashed posts can be permanently deleted.',
        ],

        // ────────────────────────────────────────────
        // Translations
        // ────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'post_not_found' => 'Cannot delete this translation: the post it is linked to cannot be found.',
                'translation_not_found' => 'The translation to delete cannot be found.',
                'last_translation_forbidden' => 'Cannot delete the last translation of the post.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'post_not_found' => 'Cannot permanently delete this translation: the post it is linked to cannot be found.',
                'translation_not_found' => 'The translation to permanently delete cannot be found.',
                'last_translation_forbidden' => 'Cannot permanently delete the last translation of the post.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],

];
